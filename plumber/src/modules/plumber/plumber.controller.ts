import { Request, Response } from 'express';
import jwt from 'jsonwebtoken';
import { asyncHandler } from '../../utils/asyncHandler';
import {
  completeRegisterPlumber,
  completeRegisterTrader,
  getPlumberById,
  getPlumberByPhone,
  getPlumberInfo,
  getPlumberPoints,
  getPlumberReport,
  getPlumbers,
  getPlumbersInfo,
  getPlumberWithdrawMoney,
  getProfile,
  loginPlumber,
  registerPlumber,
  refreshToken,
  updatePassword,
  updatePlumber,
  updatePlumberActive,
  validateOtp,
  getPlumberList,
  addReferralPoints,
  pointsForRegestration,
  getUserByPhoneAndRole
} from './plumber.service';
import { generateOtp } from '../../utils/otp.utils';
import HttpError from '../../utils/HttpError';
import { AuthenticatedRequest } from '../../@types/express';
import { Parser } from 'json2csv';
import path from 'path';
import fs from 'fs';
import SMSSender from '../../utils/smsSender';
import { Roles } from '../role/role.model';
import { getUsersByRole } from '../role/role.service';
import User from '../user/user.model';
import Plumber from './plumber.model';
import Trader from '../trader/trader.model';

export const registerHandler = asyncHandler(async (req: Request, res: Response) => {
  const { role, ...body } = req.body;
  const response = await registerPlumber(body, role || Roles.PLUMBER);
  res.status(201).json(response);
}, 'Failed to create user');

// export const registerHandler = asyncHandler(async (req: Request, res: Response) => {
//   try {
//     const body = req.body;
//     const response = await registerPlumber(body);
//     res.status(201).json(response);
//   } catch (error: any) {
//     console.error("Register Error:", error); // ⬅️ هتطبع التفاصيل في السيرفر
//     res.status(error.statusCode || 500).json({
//       status: "error",
//       message: error.message || "Failed to create plumber",
//     });
//   }
// }, "Failed to create plumber");


export const loginHandler = asyncHandler(async (req: Request, res: Response) => {
  const body = req.body;
  const response = await loginPlumber(body);
  res.status(200).json(response);
}, 'Failed to login');


export const getPlumberListHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const search = req.query.search as string | undefined;
  const plumbers = await getPlumberList(search);
  res.status(200).json({ plumbers });
}, 'Failed to get plumber list');

export const verifyPlumberPhoneHandler = asyncHandler(async (req: Request, res: Response) => {
  const { phone, otp, role = Roles.PLUMBER } = req.body;

  await validateOtp(phone, otp, role);
  res.status(200).json({ message: 'Phone verified successfully' });
}, 'Failed to verify Phone');

export const resendOtp = asyncHandler(async (req: Request, res: Response) => {
  const { phone, role } = req.body;
  const user = await getUserByPhoneAndRole(phone, role);
  if (!user) throw new HttpError('User not Found', 404);

  const isVerified = user instanceof User ? !!user.phone_verified_at : (user as any).is_verified;
  if (isVerified) return res.status(400).json({ message: 'Phone already verified' });

  const otp = generateOtp(4);
  const expirationDate = new Date();
  expirationDate.setMinutes(expirationDate.getMinutes() + 10);

  //update user
  user.otp = otp;
  user.expiration_date = expirationDate;
  await user.save();
  const smsSender = new SMSSender();
  await smsSender.sendSMS(phone, otp);
  smsSender
    .checkBalance()
    .then(balance => console.log('SMS Balance:', balance))
    .catch(error => console.error('Error Checking Balance:', error.message));

  res.status(200).json({ message: 'OTP has been sent' });
}, 'Failed to resend OTP');

export const forgetPassword = asyncHandler(async (req: Request, res: Response) => {
  const { phone, role } = req.body;
  const user = await getUserByPhoneAndRole(phone, role);
  console.log({ user });
  if (!user) throw new HttpError('User not Found', 404);

  const otp = generateOtp(4);
  const expirationDate = new Date();
  expirationDate.setMinutes(expirationDate.getMinutes() + 10);
  user.otp = otp;
  user.expiration_date = expirationDate;
  await user.save();

  const smsSender = new SMSSender();
  await smsSender.sendSMS(phone, otp);
  smsSender
    .checkBalance()
    .then(balance => console.log('SMS Balance:', balance))
    .catch(error => console.error('Error Checking Balance:', error.message));

  res.status(200).json({ message: 'otp send successfully' });
}, 'felid to forget password');



export const validatePhoneOtpAndGenerateToken = asyncHandler(async (req: Request, res: Response) => {
  const { phone, otp, role } = req.body;
  console.log({ phone, otp });
  const user = await validateOtp(phone, otp, role);
  console.log({ user: user.toJSON() });
  const userId = user instanceof User ? user.id : (user as any).user_id;
  const payload = { id: userId, role };
  const token = jwt.sign(payload, process.env.KEY!, {
    expiresIn: '5m',
  });
  res.status(200).json({ token });
}, 'felid to valid otp');

export const updateUserPasswordHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const userId = req.user!.id;
  const { password } = req.body;
  await updatePassword(userId, password);
  res.status(200).json({ message: 'password updated successfully' });
}, 'felid to update password');

export const refreshTokenHandler = asyncHandler(async (req: Request, res: Response) => {
  const authHeader = req.headers.authorization;
  if (!authHeader) throw new HttpError('Authorization header missing', 401);

  const token = authHeader.split(' ')[1];
  if (!token) throw new HttpError('Token missing', 401);

  let payload: any;
  try {
    // Try to decode even incomplete tokens
    payload = jwt.decode(token, { complete: false });

    // If decode fails, try to parse the payload part manually
    if (!payload) {
      const parts = token.split('.');
      if (parts.length >= 2) {
        const payloadPart = parts[1];
        const decoded = Buffer.from(payloadPart, 'base64').toString('utf8');
        payload = JSON.parse(decoded);
      }
    }
  } catch (err) {
    throw new HttpError('Cannot decode token', 400);
  }

  if (!payload || typeof payload !== 'object' || !payload.id) {
    throw new HttpError('Invalid token format', 400);
  }

  const response = await refreshToken(payload.id, payload.role || 'plumber');
  res.status(200).json(response);
}, 'Failed to refresh token');


export const completeRegisterPlumberReHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const userId = req.user!.id;
  const { nationality_id, nationality_image1, nationality_image2, role } = req.body;

  if (role === Roles.PLUMBER) {
    await completeRegisterPlumber(userId, {
      nationality_id,
      nationality_image1,
      nationality_image2,
    });
    res.status(200).json({ message: 'Plumber registration completed successfully' });
  } else if (role === Roles.TRADER) {
    await completeRegisterTrader(userId, {
      nationality_id,
      nationality_image1,
      nationality_image2,
    });
    res.status(200).json({ message: 'Trader registration completed successfully' });
  } else {
    throw new HttpError('Invalid role', 400);
  }
}, 'Failed to complete registration');

export const acceptPlumberHandler = asyncHandler(async (req: Request, res: Response) => {
  const { id } = req.params;
  await updatePlumberActive(id, true);
  res.status(200).json({ message: 'User updated Successfully' });
}, 'Failed to update plumber');

export const rejectPlumberHandler = asyncHandler(async (req: Request, res: Response) => {
  const { id } = req.params;
  await updatePlumberActive(id, false);
  res.status(200).json({ message: 'User updated Successfully' });
}, 'Failed to update plumber');

export const getPlumbersHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const plumbers = await getPlumbers();
  res.status(200).json({ plumbers });
}, 'Failed to update plumber');

export const getPlumberByIdHandler = asyncHandler(async (req: Request, res: Response) => {
  const { id } = req.params;
  const plumbers = await getPlumberById(Number(id));
  res.status(200).json({ plumbers });
}, 'Failed to update plumber');

export const updatePlumberHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const userId = req.user!.id;
  const data = req.body;
  const user = await updatePlumber(userId, data);
  res.status(200).json({ user, message: 'User updated Successfully' });
}, 'Failed to update plumber');

export const getProfileHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const userId = req.user!.id;
  const role = req.user!.role;
  const user = await getProfile(userId, role);
  res.status(200).json({ user });
}, 'Failed to update plumber');

export const getPlumberPointsHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const userId = req.user!.id;
  const points = await getPlumberPoints(userId);
  res.status(200).json({ points });
}, 'Failed to update plumber');

export const getPlumberWithdrawHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const userId = req.user!.id;
  const points = await getPlumberWithdrawMoney(userId);
  res.status(200).json({ points });
}, 'Failed to update plumber');

export const getPlumbersInfoHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const info = await getPlumbersInfo();
  res.status(200).json({ info });
}, 'Failed to get plumbers info');

export const getPlumberInfoHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const id = req.params.id;
  const info = await getPlumberInfo(id);
  res.status(200).json({ info });
}, 'Failed to get plumbers info');

export const getPlumberReportHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const id = req.params.id;
  const report = await getPlumberReport(id);
  res.status(200).json({ report });
}, 'Failed to get plumbers info');

export const addReferralPointsHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const { points } = req.body;

  const result = await addReferralPoints(Number(points));

  if (!result.success) {
    return res.status(400).json({ message: result.message });
  }

  res.status(200).json({ success: true, message: 'Points added successfully' });
}, 'Failed to add referral points');


export const pointsForRegestrationHandler = asyncHandler(async (req: Request, res: Response) => {
  const { start_date, end_date, points } = req.body;

  const result = await pointsForRegestration({ start_date, end_date, points });

  res.status(201).json({
    success: true,
    message: 'Registration bonus rule created successfully',
    data: result,
  });
}, 'Failed to create registration bonus rule');

export const getTradersHandler = asyncHandler(async (req: Request, res: Response) => {
  const traders = await getUsersByRole('trader');
  res.status(200).json(traders);
}, 'Failed to get traders');



// Handler to download plumber info as CSV
export const downloadPlumberInfoHandler = asyncHandler(async (req: Request, res: Response) => {
  const id = req.params.id;
  if (!id || typeof id !== 'string') {
    return res.status(400).send('Invalid or missing plumber ID');
  }

  const info = await getPlumberInfo(id);
  if (!info) {
    return res.status(404).send('Plumber not found');
  }

  const requestsCsv =
    info.requests?.map(request => {
      // Extract points for each category in the categorySum array
      const points_PPR = request.categorySum?.find(cat => cat.category_name === 'PPR')?.category_sum || 0;
      const points_PVC = request.categorySum?.find(cat => cat.category_name === 'PVC')?.category_sum || 0;
      const points_PPH = request.categorySum?.find(cat => cat.category_name === 'PPH')?.category_sum || 0;
      const points_OTHERS = request.categorySum?.find(cat => cat.category_name === 'OTHERS')?.category_sum || 0;
      const totalPoints = points_PPR + points_PVC + points_PPH;

      return {
        plumber_name: info.name,
        plumber_phone: info.phone,
        approved_requests_count: info.approved_requests_count,
        canceled_requests_count: info.canceled_requests_count,
        request_id: request.id,
        inspector_name: request.inspector?.name,
        inspector_phone: request.inspector?.phone,
        city: request.city,
        area: request.area,
        inspection_date: request.inspection_date,
        points_PPR,
        points_PVC,
        points_PPH,
        points_OTHERS,
        total_points: totalPoints,
        status: request.status,
      };
    }) || [];

  const giftsCsv =
    info.received_gifts?.map(gift => ({
      plumber_name: info.name,
      plumber_phone: info.phone,
      approved_requests_count: info.approved_requests_count,
      canceled_requests_count: info.canceled_requests_count,
      gift_name: gift.name,
      gift_points_required: gift.points_required,
    })) || [];

  // Combine all data into a single CSV
  const fields = [
    'plumber_name',
    'plumber_phone',
    'approved_requests_count',
    'canceled_requests_count',
    'request_id',
    'inspector_name',
    'inspector_phone',
    'city',
    'area',
    'inspection_date',
    'status',
    'gift_name',
    'gift_points_required',
    'points_PPR',
    'points_PVC',
    'points_PPH',
    'points_OTHERS',
    'total_points',
  ];

  const parser = new Parser({ fields });
  const csvData = parser.parse([...requestsCsv, ...giftsCsv]);

  // Prepend BOM to CSV data to ensure UTF-8 encoding with Excel
  const bom = '\uFEFF'; // BOM character for UTF-8 encoding
  const csvDataWithBom = bom + csvData; // Add BOM to CSV content

  // Save CSV to file or send it as a download
  const filePath = path.join(__dirname, `plumber_${info.user_id}_info.csv`);
  fs.writeFileSync(filePath, csvDataWithBom, 'utf8');

  res.download(filePath, err => {
    if (err) {
      console.error('Error sending file:', err);
      return res.status(500).send('Failed to download CSV file');
    }
    // Cleanup: Delete file after sending
    fs.unlinkSync(filePath);
  });
}, 'felid to get user data');

export const downloadPlumberReportHandler = asyncHandler(async (req: Request, res: Response) => {
  const id = req.params.id;
  if (!id || typeof id !== 'string') {
    return res.status(400).send('Invalid or missing plumber ID');
  }

  // Fetch plumber details
  const info = await getPlumberReport(id);
  if (!info) {
    return res.status(404).send('Plumber not found');
  }

  // Process requests to generate CSV data
  const requestsCsv =
    info.requests?.flatMap(request => {
      // Map each item in the request's items array
      return request.items?.map(item => ({
        plumber_name: info.name,
        plumber_phone: info.phone,
        inspector_name: request.inspector?.name,
        inspector_phone: request.inspector?.phone,
        request_id: request.id,
        inspection_date: request.inspection_date.toISOString().split('T')[0],

        top_category_name: item.topCategoryName,
        items_name: item.category_name,
        item_count: item.category_count,
        category_points: item.category_points,

        total_points: item.category_points * item.category_count,
      }));
    }) || [];

  // Define the CSV fields
  const fields = [
    'plumber_name',
    'plumber_phone',
    'inspector_name',
    'inspector_phone',
    'request_id',
    'inspection_date',
    'top_category_name',
    'items_name',
    'item_count',
    'category_points',
    'total_points',
  ];

  // Create CSV data
  const parser = new Parser({ fields });
  const csvData = parser.parse(requestsCsv);

  // Prepend BOM to CSV data to ensure UTF-8 encoding with Excel
  const bom = '\uFEFF'; // BOM character for UTF-8 encoding
  const csvDataWithBom = bom + csvData; // Add BOM to CSV content

  // Set file path
  const filePath = path.join(__dirname, `plumber_${info.user_id}_report.csv`);
  fs.writeFileSync(filePath, csvDataWithBom, { encoding: 'utf8' });

  // Set response headers for downloading the CSV
  res.header('Content-Type', 'text/csv; charset=utf-8');
  res.header('Content-Disposition', `attachment; filename="plumber_${info.user_id}_report.csv"`);

  // Send the CSV file for download
  res.download(filePath, err => {
    if (err) {
      console.error('Error downloading the file:', err);
      return res.status(500).send('Failed to download CSV file');
    }
    // Optionally clean up by deleting the file after sending
    fs.unlinkSync(filePath);
  });
}, 'failed to get user data');
