/* eslint-disable @typescript-eslint/no-explicit-any */
import bcrypt from 'bcrypt';
import jwt from 'jsonwebtoken';
import { ICreatePlumber } from './dto/create-plumber.dto';
import Plumber, { PlumberStatus } from './plumber.model';
import User from '../user/user.model';
import HttpError from '../../utils/HttpError';
import { ILoginPlumber } from './dto/login-plumber.dto';
import { generateOtp } from '../../utils/otp.utils';
import { IUpdatePlumber } from './dto/update-plumber.dto';
import { saveImages, viewImages } from '../../utils/imageUtils';
import { assignRole, getRole } from '../role/role.service';
import { Roles } from '../role/role.model';
import { Sequelize } from 'sequelize';
import { generateUniqueReferralCode } from '../../utils/generateReferCode'
import { Op } from 'sequelize';
import RegistrationBonusRule from '../user/registration_bonus.model';
import ReferralConfig from '../user/ReferralConfig.model';
import InspectionRequest, { RequestStatus } from '../inspectionRequest/inspection_request.model';
import { PendingBonus } from '../user/pending-bonus.model';
import Trader from '../trader/trader.model';
import EnvoySetting from '../envoy/envoy.model';
import DeviceKey from '../user/device_key.model';


import {
  formatPlumberReportRequests,
  formatPlumberRequests,
  getPlumberDetails,
  getPlumberReportDetails,
} from './plumber.utils';
import { sendPushNotification } from '../../utils/notification';
import SMSSender from '../../utils/smsSender';

// export const registerPlumber = async (newPlumber: ICreatePlumber) => {
//   const { name, password, phone, city, area, referralCode } = newPlumber;

//   let referrer: User | null = null;
//   if (referralCode) {
//     referrer = await User.findOne({ where: { refer_code: referralCode } });
//     if (!referrer) {
//       throw new HttpError('Referral code is invalid', 400);
//     }
//   }


//   const hashedPassword = await bcrypt.hash(password, 10);
//   const userReferCode = await generateUniqueReferralCode();
//   const user = await User.create({
//     name,
//     password: hashedPassword,
//     phone,
//     is_active: false,
//     refer_code: userReferCode,
//   });

//   if (!user) throw new HttpError('User not created', 404);


//   const plumber = await Plumber.create({
//     user_id: user.id,
//     city,
//     area,
//   });


//   const role = await assignRole(user.id, Roles.PLUMBER);


//   const otp = generateOtp(4);
//   const expirationDate = new Date();
//   expirationDate.setMinutes(expirationDate.getMinutes() + 10);
//   plumber.otp = otp;
//   plumber.expiration_date = expirationDate;
//   await plumber.save();


//   const bonusRule = await RegistrationBonusRule.findOne({
//     where: {
//       start_date: { [Op.lte]: new Date() },
//       end_date: { [Op.gte]: new Date() },
//     },
//     order: [['created_at', 'DESC']],
//   });


//   if (bonusRule && referrer) {
//     await PendingBonus.create({
//       user_id: referrer.id,         // صاحب الكود
//       referred_user_id: user.id,    // الشخص الجديد اللي سجل
//       points: bonusRule.points,
//     });

//     console.log(
//       `Pending bonus (${bonusRule.points}) saved for referrer ${referrer.id} because of new user ${user.id}`
//     );
//   }


//   const smsSender = new SMSSender();
//   await smsSender.sendSMS(user.phone, otp);
//   smsSender
//     .checkBalance()
//     .then(balance => console.log('SMS Balance:', balance))
//     .catch(error => console.error('Error Checking Balance:', error.message));


//   const payload = { id: user.id, role: Roles.PLUMBER };
//   const token = jwt.sign(payload, process.env.KEY!);

//   return { token, role, ...user.get(), ...plumber.get() };
// };

// last registration flow
/*
export const registerPlumber = async (newPlumber: ICreatePlumber) => {
  const { name, password, phone, city, area, referralCode } = newPlumber;

  let referrer: User | null = null;

  if (referralCode) {
    referrer = await User.findOne({ where: { refer_code: referralCode } });
    if (!referrer) {
      throw new HttpError('Referral code is invalid', 400);
    }
  }

  const hashedPassword = await bcrypt.hash(password, 10);
  const userReferCode = await generateUniqueReferralCode();

  const user = await User.create({
    name,
    password: hashedPassword,
    phone,
    is_active: false,
    refer_code: userReferCode,
  });

  if (!user) throw new HttpError('User not created', 404);

  const plumber = await Plumber.create({
    user_id: user.id,
    city,
    area,
  });

  const role = await assignRole(user.id, Roles.PLUMBER);

  const otp = generateOtp(4);
  const expirationDate = new Date();
  expirationDate.setMinutes(expirationDate.getMinutes() + 10);

  plumber.otp = otp;
  plumber.expiration_date = expirationDate;
  await plumber.save();

  const bonusRule = await RegistrationBonusRule.findOne({
    where: {
      start_date: { [Op.lte]: new Date() },
      end_date: { [Op.gte]: new Date() },
    },
    order: [['created_at', 'DESC']],
  });

  if (bonusRule) {
    await PendingBonus.create({
      new_user_id: user.id,
      referrer_id: referrer ? referrer.id : null,
      points: bonusRule.points,
    });

    console.log(
      `Pending bonus (${bonusRule.points}) saved for new user ${user.id} with referrer ${referrer?.id || 'none'}`
    );
  }

  const smsSender = new SMSSender();
  await smsSender.sendSMS(user.phone, otp);

  smsSender
    .checkBalance()
    .then(balance => console.log('SMS Balance:', balance))
    .catch(error => console.error('Error Checking Balance:', error.message));

  const payload = { id: user.id, role: Roles.PLUMBER };
  const token = jwt.sign(payload, process.env.KEY!);

  return { token, role, ...user.get(), ...plumber.get() };
};
*/

// new registration flow handles both plumber and trader registration
export const registerPlumber = async (newPlumber: ICreatePlumber, role: Roles = Roles.PLUMBER) => {
  const { name, password, phone, city, area, referralCode, device_token, device_type } = newPlumber;

  let referrer: User | null = null;

  if (referralCode) {
    referrer = await User.findOne({ where: { refer_code: referralCode } });
    if (!referrer) {
      throw new HttpError('Referral code is invalid', 400);
    }
  }

  const hashedPassword = await bcrypt.hash(password, 10);
  const userReferCode = await generateUniqueReferralCode();

  const user = await User.create({
    name,
    password: hashedPassword,
    phone,
    is_active: false,
    refer_code: userReferCode,
  });

  if (!user) throw new HttpError('User not created', 404);

  if (device_token) {
    await DeviceKey.create({
      user_id: user.id,
      key: device_token,
      device_type: device_type || 'android',
    });
  }

  let plumber = null;
  let trader = null;

  if (role === Roles.PLUMBER) {
    plumber = await Plumber.create({
      user_id: user.id,
      city,
      area,
    });
  } else if (role === Roles.TRADER) {
    trader = await Trader.create({
      user_id: user.id,
      city,
      area,
    });
  }

  const assignedRole = await assignRole(user.id, role);

  if (role === Roles.PLUMBER && plumber) {
    const otp = generateOtp(4);
    const expirationDate = new Date();
    expirationDate.setMinutes(expirationDate.getMinutes() + 10);

    plumber.otp = otp;
    plumber.expiration_date = expirationDate;
    await plumber.save();
  } else if (role === Roles.TRADER && trader) {
    const otp = generateOtp(4);
    const expirationDate = new Date();
    expirationDate.setMinutes(expirationDate.getMinutes() + 10);

    trader.otp = otp;
    trader.expiration_date = expirationDate;
    await trader.save();
  }

  const bonusRule = await RegistrationBonusRule.findOne({
    where: {
      start_date: { [Op.lte]: new Date() },
      end_date: { [Op.gte]: new Date() },
    },
    order: [['created_at', 'DESC']],
  });

  if (bonusRule) {
    await PendingBonus.create({
      new_user_id: user.id,
      referrer_id: referrer ? referrer.id : null,
      points: bonusRule.points,
    });

    console.log(
      `Pending bonus (${bonusRule.points}) saved for new user ${user.id} with referrer ${referrer?.id || 'none'}`
    );
  }

  if (role === Roles.PLUMBER && plumber) {
    const smsSender = new SMSSender();
    await smsSender.sendSMS(user.phone, plumber!.otp!);
    smsSender
      .checkBalance()
      .then(balance => console.log('SMS Balance:', balance))
      .catch(error => console.error('Error Checking Balance:', error.message));
  } else if (role === Roles.TRADER && trader) {
    const smsSender = new SMSSender();
    await smsSender.sendSMS(user.phone, trader!.otp!);
    smsSender
      .checkBalance()
      .then(balance => console.log('SMS Balance:', balance))
      .catch(error => console.error('Error Checking Balance:', error.message));
  }

  const payload = { id: user.id, role };
  const token = jwt.sign(payload, process.env.KEY!);

  if (role === Roles.PLUMBER && plumber) {
    return { token, role: assignedRole, ...user.get(), ...plumber.get() };
  } else if (role === Roles.TRADER && trader) {
    return { token, role: assignedRole, ...user.get(), ...trader.get() };
  } else {
    return { token, role: assignedRole, ...user.get() };
  }
};


// export const loginPlumber = async (data: ILoginPlumber) => {
//   const { password, phone } = data;
//   const user = await User.findOne({ where: { phone } });

//   if (!user) throw new HttpError('Incorrect phone or password', 404);

//   const storedHash = user.password.startsWith('$2y$')
//     ? user.password.replace('$2y$', '$2b$')
//     : user.password;

//   const isPasswordValid = await bcrypt.compare(password, storedHash);
//   if (!isPasswordValid) throw new HttpError('Incorrect phone or password', 401);


//   const role = await getRole(user.id);

//   if (role === Roles.Envoy) {
//     const payload = { id: user.id, role: Roles.Envoy };
//     const token = jwt.sign(payload, process.env.KEY!);

//     user.last_login_token = token;
//     await user.save();

//     return { token, user: { ...user.toJSON(), role } };
//   }

//   const payload = { id: user.id, role: Roles.PLUMBER };
//   const token = jwt.sign(payload, process.env.KEY!);

//   user.last_login_token = token;
//   await user.save();

//   const plumber = await Plumber.findOne({
//     where: { user_id: user.id },
//     attributes: {
//       exclude: ['otp', 'expiration_date'],
//     },
//   });

//   if (!plumber) throw new HttpError('plumber not found', 404);
//   if (user.status === PlumberStatus.PENDING) throw new HttpError('plumber not approved yet', 401);
//   if (user.status === PlumberStatus.REJECTED) throw new HttpError('plumber rejected', 401);

//   const plumberData = plumber.toJSON();
//   const plumberResponse = {
//     name: user.name,
//     role,
//     is_completed: true,
//     is_active: user.is_active,
//     ...plumberData,
//     image: plumberData.image ? (viewImages(plumberData.image) as string) : '',
//   };

//   if (!plumber.nationality_id || !plumber.nationality_image1 || !plumber.nationality_image2) {
//     plumberResponse.is_completed = true;
//   }

//   delete plumberResponse.nationality_id;
//   delete plumberResponse.nationality_image1;
//   delete plumberResponse.nationality_image2;

//   return { token, user: plumberResponse };
// };


export const loginPlumber = async (data: ILoginPlumber) => {
  const { password, phone, device_token, device_type } = data;
  const user = await User.findOne({ where: { phone } });

  if (!user) throw new HttpError('Incorrect phone or password', 404);

  const storedHash = user.password.startsWith('$2y$')
    ? user.password.replace('$2y$', '$2b$')
    : user.password;

  const isPasswordValid = await bcrypt.compare(password, storedHash);
  if (!isPasswordValid) throw new HttpError('Incorrect phone or password', 401);

  const role = await getRole(user.id);

  const payload = { id: user.id, role };
  const token = jwt.sign(payload, process.env.KEY!, { expiresIn: '7d' });

  user.last_login_token = token;
  if (device_token) {
    user.device_token = device_token;
    await DeviceKey.findOrCreate({
      where: { user_id: user.id, key: device_token },
      defaults: { device_type: device_type || 'android' },
    });
  }
  await user.save();

  if (role === Roles.Envoy || role === Roles.TRADER) {
    if (role === Roles.Envoy) {
      const envoySetting = await EnvoySetting.findOne({ where: { user_id: user.id } });
      return { token, user: { ...user.toJSON(), role, envoySetting } };
    }
    if (role === Roles.TRADER) {
      if (user.status === PlumberStatus.PENDING) throw new HttpError('trader not approved yet', 401);
      if (user.status === PlumberStatus.REJECTED) throw new HttpError('trader rejected', 401);
    }
    return { token, user: { ...user.toJSON(), role } };
  }

  const plumber = await Plumber.findOne({
    where: { user_id: user.id },
    attributes: { exclude: ['otp', 'expiration_date'] },
  });

  if (!plumber) throw new HttpError('plumber not found', 404);
  if (user.status === PlumberStatus.PENDING) throw new HttpError('plumber not approved yet', 401);
  if (user.status === PlumberStatus.REJECTED) throw new HttpError('plumber rejected', 401);

  const plumberData = plumber.toJSON();
  const plumberResponse: any = {
    name: user.name,
    role,
    is_completed: true,
    is_active: user.is_active,
    ...plumberData,
    image: plumberData.image ? (viewImages(plumberData.image) as string) : '',
  };

  if (!plumber.nationality_id || !plumber.nationality_image1 || !plumber.nationality_image2) {
    plumberResponse.is_completed = false;
  }

  delete plumberResponse.nationality_id;
  delete plumberResponse.nationality_image1;
  delete plumberResponse.nationality_image2;

  return { token, user: plumberResponse };
};



export const getUserByPhoneAndRole = async (phone: string, role?: string) => {
  const user = await User.findOne({ where: { phone } });
  if (!user) {
    throw new HttpError('User not Found', 404);
  }

  const assignedRole = await getRole(user.id);
  if (role && assignedRole !== role) {
    throw new HttpError('User not registered for this role', 400);
  }

  const effectiveRole = role || assignedRole;

  if (effectiveRole === Roles.PLUMBER) {
    const plumber = await Plumber.findOne({
      where: {
        user_id: user.id,
      },
    });
    return plumber;
  } else if (effectiveRole === Roles.TRADER) {
    const trader = await Trader.findOne({
      where: {
        user_id: user.id,
      },
    });
    return trader;
  } else if (effectiveRole === Roles.Envoy) {
    return user;
  }
  throw new HttpError('Invalid role', 400);
};

export const getPlumberByPhone = async (phone: string) => {
  const user = await User.findOne({ where: { phone } });
  if (!user) {
    throw new HttpError('User not Found', 404);
  }
  const plumber = await Plumber.findOne({
    where: {
      user_id: user.id,
    },
  });
  return plumber;
};

export const getPlumberById = async (userId: number) => {
  console.log({ userId });
  const user = await User.findOne({ where: { id: userId } });
  if (!user) {
    throw new HttpError('User not Found', 404);
  }

  const plumber = await Plumber.findOne({
    where: {
      user_id: user.id,
    },
  });
  if (!plumber) {
    throw new HttpError('Plumber not Found', 404);
  }
  const existPlumber = plumber.toJSON();
  return {
    ...existPlumber,

    image: existPlumber.image ? (viewImages(existPlumber.image) as string) : '',
    nationality_image1: existPlumber?.nationality_image1 ? (viewImages(existPlumber.nationality_image1) as string) : '',
    nationality_image2: existPlumber?.nationality_image2 ? (viewImages(existPlumber.nationality_image2) as string) : '',
  };
};

export const validateOtp = async (phone: string, otp: string, role?: string) => {
  const user = await getUserByPhoneAndRole(phone, role);
  console.log({ user });
  if (!user?.otp) {
    throw new HttpError('OTP has not been sent', 400);
  }

  if (user.otp !== otp) {
    throw new HttpError('Invalid OTP', 400);
  }

  if (user.expiration_date) {
    const otpExpiryTime = new Date(user.expiration_date).getTime();
    if (otpExpiryTime < Date.now()) {
      throw new HttpError('OTP has expired', 400);
    }
  }

  if (user instanceof User) {
    user.phone_verified_at = new Date();
  } else {
    user.is_verified = true;
  }
  user.otp = null;
  user.expiration_date = null;
  await user.save();
  return user;
};

export const completeRegisterPlumber = async (
  userId: string,
  data: { nationality_id: string; nationality_image1: string; nationality_image2: string },
) => {
  const { nationality_id, nationality_image1, nationality_image2 } = data;
  const img1 = saveImages(nationality_image1) as string;
  const img2 = saveImages(nationality_image2) as string;
  const [numOfRow] = await Plumber.update(
    {
      nationality_id,
      nationality_image1: img1,
      nationality_image2: img2,
    },
    {
      where: { user_id: userId },
    },
  );
  console.log({ numOfRow });
  if (!numOfRow) {
    throw new HttpError('User Not found', 404);
  }
  return;
};

export const completeRegisterTrader = async (
  userId: string,
  data: { nationality_id: string; nationality_image1: string; nationality_image2: string },
) => {
  const { nationality_id, nationality_image1, nationality_image2 } = data;
  const img1 = saveImages(nationality_image1) as string;
  const img2 = saveImages(nationality_image2) as string;
  const [numOfRow] = await Trader.update(
    {
      nationality_id,
      nationality_image1: img1,
      nationality_image2: img2,
    },
    {
      where: { user_id: userId },
    },
  );
  console.log({ numOfRow });
  if (!numOfRow) {
    throw new HttpError('User Not found', 404);
  }
  return;
};

export const updatePlumberActive = async (userId: string, is_active: boolean) => {
  const user = await User.findOne({ where: { id: userId } });
  if (!user) {
    throw new HttpError('User Not found', 404);
  }

  const plumber = await Plumber.findOne({ where: { user_id: userId } });
  if (!plumber) {
    throw new HttpError('Plumber Not found', 404);
  }

  user.is_active = is_active;
  if (user.status !== PlumberStatus.PENDING) {
    throw new HttpError('User is not pending', 400);
  }
  const newStatus = is_active ? PlumberStatus.APPROVED : PlumberStatus.REJECTED;
  user.status = newStatus;

  await user.save();

  // Trigger notification to envoy if exists
  if (plumber.inspector_id) {
    const title = 'Registration Update';
    const body = `The plumber ${user.name} you registered has been ${newStatus.toLowerCase()}.`;
    sendPushNotification(Number(plumber.inspector_id), title, body);
  }

  return;
};

export const getPlumbers = async () => {
  const result = await Plumber.findAndCountAll({
    include: [
      {
        model: User,
        required: true,
      },
    ],
  });

  const { rows, count } = result;

  return {
    total_plumber: count,
    plumbers: rows,
  };
};

export const getProfile = async (id: string, role: string) => {
  const user = await User.findByPk(id, {
    attributes: ['id', 'name', 'phone', 'refer_code'],
  });
  if (!user) {
    throw new HttpError('User not found', 404);
  }

  if (role === Roles.PLUMBER) {
    const plumber = await Plumber.findOne({
      where: { user_id: id },
      attributes: {
        exclude: ['nationality_image1', 'nationality_image2', 'otp', 'expiration_date'],
      },
    });

    if (!plumber) {
      throw new HttpError('Plumber not found', 404);
    }

    return {
      refer_code: user?.refer_code,

      ...user.toJSON(),
      role,
      ...plumber.toJSON(),
      image: plumber.image ? (viewImages(plumber.image) as string) : '',
    };
  }
  return {
    ...user.toJSON(),
    role,
  };
};

export const getPlumberPoints = async (id: string) => {
  const points = await Plumber.findOne({
    where: { user_id: id },
    attributes: ['id', 'instant_withdrawal', 'fixed_points', 'gift_points'],
  });

  if (!points) {
    throw new HttpError('Plumber not found', 404);
  }

  return { ...points.toJSON() };
};

export const getPlumberWithdrawMoney = async (id: string) => {
  const points = await Plumber.findOne({
    where: { user_id: id },
    attributes: ['instant_withdrawal', 'withdraw_money'],
  });

  if (!points) {
    throw new HttpError('Plumber not found', 404);
  }

  return { ...points.toJSON(), user_id: id };
};
export const updatePlumber = async (userId: string, newPlumber: IUpdatePlumber) => {
  const { name, phone, city, area, image } = newPlumber;

  const user = await User.findOne({
    where: {
      id: userId,
    },
  });

  if (!user) throw new HttpError('User not found', 404);

  if (name) user.name = name;
  if (phone) user.phone = phone;
  await user.save();

  // Update fields on the Plumber model
  if (city || image || area) {
    const plumber = await Plumber.findOne({ where: { user_id: userId } });
    if (!plumber) {
      throw new HttpError('Plumber not found', 404);
    }

    if (city) plumber.city = city;
    if (area) plumber.area = area;
    if (image) plumber.image = saveImages(image) as string;
    await plumber.save();
  }

  // Fetch updated data
  const updatedPlumber = await Plumber.findOne({
    where: { user_id: userId },
    include: [{ model: User, as: 'user', attributes: ['name', 'phone'] }],
  });

  if (!updatedPlumber || !updatedPlumber.user) throw new HttpError('Plumber or user not found after update', 404);

  return {
    id: updatedPlumber.user_id,
    name: updatedPlumber.user.name,
    phone: updatedPlumber.user.phone,
    image: updatedPlumber.image ? (viewImages(updatedPlumber.image) as string) : '',
    city: updatedPlumber.city,
    area: updatedPlumber.area,
    nationality_id: updatedPlumber.nationality_id,
  };
};

export const getPlumbersInfo = async () => {
  const plumbers = await Plumber.findAll({
    attributes: ['user_id', 'instant_withdrawal', 'fixed_points', 'gift_points'],
    include: [
      {
        model: User,
        attributes: [
          'id',
          'name',
          'phone',

          [
            Sequelize.literal(
              `(SELECT COUNT(*) FROM \`inspection_requests\` 
                WHERE \`inspection_requests\`.\`requestor_id\` = \`User\`.\`id\` 
                AND \`inspection_requests\`.\`status\` = 'APPROVED')`,
            ),
            'approved_requests_count',
          ],
          [
            Sequelize.literal(
              `(SELECT COUNT(*) FROM \`inspection_requests\` 
                WHERE \`inspection_requests\`.\`requestor_id\` = \`User\`.\`id\` 
                AND \`inspection_requests\`.\`status\` = 'CANCELLED')`,
            ),
            'canceled_requests_count',
          ],
        ],
        as: 'user',
      },
    ],
  });

  return plumbers;
};

///  get plumber report
export const getPlumberInfo = async (id: string) => {
  const plumber = await getPlumberDetails(id);
  if (!plumber) return null;

  const plumberData = plumber.toJSON();
  const { user, ...rest } = plumberData;

  const receivedGifts =
    user?.received_gifts?.map(gift => ({
      name: gift.gift?.name,
      points_required: gift.gift?.points_required,
    })) || [];
  delete user?.received_gifts;

  const requestsWithCategorySum = user?.requests ? await formatPlumberRequests(user?.requests) : [];

  return {
    ...rest,
    ...user,
    refer_code: user?.refer_code,
    approved_requests_count: user?.approved_requests_count || 0,
    canceled_requests_count: user?.canceled_requests_count || 0,
    gift_points: plumberData.gift_points,
    requests: requestsWithCategorySum,
    received_gifts: receivedGifts,

  };
};

export const getPlumberReport = async (id: string) => {
  const plumber = await getPlumberReportDetails(id);
  if (!plumber) return null;

  const plumberData = plumber.toJSON();
  const { user, ...rest } = plumberData;

  const requestsWithCategory = user?.requests ? await formatPlumberReportRequests(user?.requests) : [];

  return {
    ...rest,
    ...user,
    requests: requestsWithCategory,
  };
};

export const updatePassword = async (userId: string, newPassword: string) => {
  const user = await User.findOne({ where: { id: userId } });
  if (!user) {
    throw new HttpError('User Not found', 404);
  }
  const hashedPassword = await bcrypt.hash(newPassword, 10);
  user.password = hashedPassword;
  await user.save();
  return user;
};


export const getPlumberList = async (search?: string) => {
  const whereClause = search
    ? {
      [Op.or]: [
        { '$user.name$': { [Op.like]: `%${search}%` } },
        { '$user.phone$': { [Op.like]: `%${search}%` } },
      ],
    }
    : {};

  const plumbers = await Plumber.findAll({
    where: whereClause,
    attributes: ['id', 'user_id'],
    include: [
      {
        model: User,
        as: 'user',
        attributes: ['name', 'phone'],
      },
    ],
  });

  if (!plumbers || plumbers.length === 0) {
    return [];
  }

  return plumbers.map((plumber) => ({
    id: plumber.id,
    userId: plumber.user_id,
    firstName: plumber.user?.name,
    phone: plumber.user?.phone,
  }));
};




export const addReferralPoints = async (
  points: number
): Promise<{ success: boolean; message?: string }> => {
  const existingConfig = await ReferralConfig.findOne();

  if (existingConfig) {
    existingConfig.referral_point = points;
    await existingConfig.save();
    return { success: true };
  }

  await ReferralConfig.create({ referral_point: points });
  return { success: true };
};




export const pointsForRegestration = async ({
  start_date,
  end_date,
  points,
}: {
  start_date: Date;
  end_date: Date;
  points: number;
}): Promise<RegistrationBonusRule> => {
  const rule = await RegistrationBonusRule.create({
    start_date,
    end_date,
    points,
  });

  return rule;
};

export const refreshToken = async (userId: string, role: string) => {
  const user = await User.findOne({ where: { id: userId } });
  if (!user) {
    throw new HttpError('User not found', 404);
  }

  const payload = { id: user.id, role };
  const token = jwt.sign(payload, process.env.KEY!);

  user.last_login_token = token;
  await user.save();

  if (role === Roles.PLUMBER) {
    const plumber = await Plumber.findOne({ where: { user_id: user.id } });
    if (!plumber) throw new HttpError('Plumber not found', 404);

    return {
      token,
      user: {
        name: user.name,
        role,
        is_active: user.is_active,
        ...plumber.toJSON(),
      },
    };
  }

  return { token, user: { ...user.toJSON(), role } };
};

