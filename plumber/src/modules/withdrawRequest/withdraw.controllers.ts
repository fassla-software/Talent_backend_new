import ExcelJS, { Cell, Column } from 'exceljs';
import fs from 'fs';
import { Response, Request } from 'express';
import { asyncHandler } from '../../utils/asyncHandler';
import {
  addWithdrawRequest,
  getUserWithdrawRequests,
  getWithdrawRequests,
  updateWithdrawRequest,
} from './withdraw.service';
import { AuthenticatedRequest } from '../../@types/express';
import path from 'path';
import WithdrawRequest, { WithdrawRequestRow, WithdrawRequestStatus } from './withdraw.model';
import { formatPhoneNumber } from './withdraw.utils';
import * as XLSX from 'xlsx';
import sequelize from '../../config/db';
import Plumber from '../plumber/plumber.model';

export const addWithdrawRequestHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const user_id = req.user!.id;
  const response = await addWithdrawRequest({ user_id, ...req.body });
  res.status(200).json(response);
}, 'Failed to create withdraw request');

export const updateWithdrawRequestHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const id = req.params!.id as string;
  const user_id = req.user!.id;
  const body = req.body;
  const response = await updateWithdrawRequest(id, user_id, body);
  res.status(200).json(response);
}, 'Failed to update withdraw request');

export const getWithdrawRequestHandler = asyncHandler(async (req: Request, res: Response) => {
  const status = req.query.status as string;
  const response = await getWithdrawRequests(status);
  res.status(200).json(response);
}, 'Failed to get withdraw requests');

export const getUserWithdrawRequestHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const status = req.query.status as string;
  const id = req.user!.id;
  const response = await getUserWithdrawRequests(id, status);
  res.status(200).json(response);
}, 'Failed to get withdraw requests');

// export const downloadWithdrawRequestHandler = asyncHandler(async (req: Request, res: Response) => {
//   const status = req.query.status as string;
//   const requests = await getWithdrawRequests(status);

//   if (!requests.length) {
//     return res.status(404).send('Requests not found');
//   }

//   // Preprocess the data to ensure dates are in ISO string format
//   const processedRequests = requests.map(request => ({
//     ...request.toJSON(),
//     request_date: request.request_date ? new Date(request.request_date).toISOString() : null,
//   }));
//   console.log({ processedRequests });
//   // Define the CSV fields and map to the response structure
//   const fields = [
//     { label: 'Request ID', value: 'id' },
//     { label: 'Requester Name', value: 'requestor.name' },
//     { label: 'Requester Phone', value: 'requestor.phone' },
//     { label: 'Amount', value: 'amount' },
//     { label: 'Status', value: 'status' },
//     { label: 'Payment Identifier', value: 'payment_identifier' },
//     { label: 'Transaction Type', value: 'transaction_type' },
//     { label: 'Request Date', value: 'request_date' },
//   ];

//   // Parse the data to CSV format
//   const parser = new Parser({ fields });
//   const csvData = parser.parse(processedRequests);

//   // Prepend BOM to CSV data to ensure proper encoding
//   const bom = '\uFEFF'; // BOM character for UTF-8 encoding
//   const csvDataWithBom = bom + csvData;

//   // Set file path for the CSV file
//   const filePath = path.join(__dirname, `withdraw_requests_${Date.now()}.csv`);
//   fs.writeFileSync(filePath, csvDataWithBom, { encoding: 'utf8' });

//   // Set headers for downloading the file
//   res.header('Content-Type', 'text/csv; charset=utf-8');
//   res.header('Content-Disposition', `attachment; filename="withdraw_requests_${Date.now()}.csv"`);

//   // Send the file for download
//   res.download(filePath, err => {
//     if (err) {
//       console.error('Error downloading the file:', err);
//       return res.status(500).send('Failed to download CSV file');
//     }

//     // Optionally clean up the file after sending it
//     fs.unlinkSync(filePath);
//   });
// // }, 'Failed to get withdraw requests');

// export const uploadWithdrawRequestHandler = asyncHandler(async (req: Request, res: Response) => {
//   if (!req.file) return res.status(400).json({ error: 'No file uploaded' });

//   const failedRows: { row: WithdrawRequestRow; error: string }[] = [];
//   const csvData = await csvtojson().fromString(req.file.buffer.toString('utf8'));

//   await Promise.all(
//     csvData.map(async row => {
//       const cleanedRow: WithdrawRequestRow = {
//         id: row['Request ID']?.trim() ?? '',
//         status: row['Status']?.trim() ?? '',
//         amount: row['Amount']?.trim() ?? '',
//         payment_identifier: row['Payment Identifier']?.trim() ?? '',
//         transaction_type: row['Transaction Type']?.trim() ?? '',
//       };

//       if (
//         !cleanedRow.id ||
//         !cleanedRow.status ||
//         !cleanedRow.amount ||
//         !cleanedRow.payment_identifier ||
//         !cleanedRow.transaction_type
//       ) {
//         failedRows.push({ row: cleanedRow, error: 'Missing required fields' });
//         return null; // Skip invalid rows
//       }

//       if (!Object.values(WithdrawRequestStatus).includes(cleanedRow.status as WithdrawRequestStatus)) {
//         failedRows.push({ row: cleanedRow, error: 'Invalid status' });
//       }

//       try {
//         const request = await WithdrawRequest.findByPk(cleanedRow.id);
//         if (request) {
//           await request.update({ status: cleanedRow.status, processed_date: new Date() });
//           return cleanedRow; // Successfully processed
//         } else {
//           failedRows.push({ row: cleanedRow, error: 'Request not found' });
//         }
//       } catch (err) {
//         failedRows.push({ row: cleanedRow, error: 'Error updating request' });
//       }

//       return null;
//     }),
//   );

//   if (failedRows.length > 0) {
//     return res.status(200).json({
//       message: 'Some rows processed successfully, but there were errors',
//       failedRows,
//     });
//   }

//   res.status(200).json({
//     message: 'CSV processed successfully, no failed rows',
//   });
// }, 'Failed to upload withdraw requests');

export const downloadWithdrawRequestHandler = asyncHandler(async (req: Request, res: Response) => {
  const status = req.query.status as string;
  const requests = await getWithdrawRequests(status);

  if (!requests.length) {
    return res.status(404).send('Requests not found');
  }

  // Preprocess the data to ensure dates are in ISO string format
  const processedRequests = requests.map(request => ({
    ...request,
    payment_identifier: `"${formatPhoneNumber(request.payment_identifier)}"`,
    request_date: request.request_date ? new Date(request.request_date).toISOString() : null,
    requestor: {
      ...request.requestor,
      phone: `"${formatPhoneNumber(request.requestor?.phone)}"`,
    },
  }));

  // Create a new Excel workbook
  const workbook = new ExcelJS.Workbook();
  const worksheet = workbook.addWorksheet('Withdraw Requests');

  // Define columns with headers and set widths
  worksheet.columns = [
    { header: 'Request ID', key: 'id', width: 15 },
    { header: 'Requester Name', key: 'name', width: 25 },
    { header: 'Requester Phone', key: 'phone', width: 20 },
    { header: 'Amount', key: 'amount', width: 15 },
    { header: 'Status', key: 'status', width: 15 },
    { header: 'Card Image', key: 'image', width: 20 },
    { header: 'Payment Identifier', key: 'payment_identifier', width: 20 },
    { header: 'Transaction Type', key: 'transaction_type', width: 20 },
    { header: 'Request Date', key: 'request_date', width: 20 },
  ];

  // Add data to the worksheet
  processedRequests.forEach(request => {
    worksheet.addRow({
      id: request.id,
      name: request.requestor?.name,
      phone: request.requestor?.phone,
      amount: request.amount,
      status: request.status,
      payment_identifier: request.payment_identifier,
      transaction_type: request.transaction_type,
      request_date: request.request_date,
      image: request.image,
    });
  });

  // Protect specific columns (non-editable)
  ['A', 'B', 'C', 'D'].forEach(colLetter => {
    const col: Column = worksheet.getColumn(colLetter);
    col.eachCell((cell: Cell) => {
      cell.protection = { locked: true };
    });
  });

  ['E', 'F', 'G', 'H', 'I'].forEach(colLetter => {
    const col: Column = worksheet.getColumn(colLetter);
    col.eachCell((cell: Cell) => {
      cell.protection = { locked: false };
    });
  });

  worksheet.protect('', {
    selectLockedCells: true,
    selectUnlockedCells: true,
    formatColumns: true, // Allows column width adjustment
    formatRows: true, // Allows row height adjustment
    insertColumns: false, // Prevents adding columns
    deleteColumns: false, // Prevents deleting columns
  });

  // Save the workbook to a file
  const filePath = path.join(__dirname, `withdraw_requests_${Date.now()}.xlsx`);
  await workbook.xlsx.writeFile(filePath);

  // Set headers for downloading the file
  res.header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  res.header('Content-Disposition', `attachment; filename="withdraw_requests_${Date.now()}.xlsx"`);

  // Send the file for download
  res.download(filePath, err => {
    if (err) {
      console.error('Error downloading the file:', err);
      return res.status(500).send('Failed to download Excel file');
    }

    // Optionally clean up the file after sending
    fs.unlinkSync(filePath);
  });
}, 'Failed to get withdraw requests');

export const uploadWithdrawRequestHandler = asyncHandler(async (req: Request, res: Response) => {
  if (!req.file) return res.status(400).json({ error: 'No file uploaded' });

  const failedRows: { row: WithdrawRequestRow; error: string }[] = [];

  // Read the XLSX file
  const workbook = XLSX.read(req.file.buffer);
  const sheetName = workbook.SheetNames[0];
  const sheet = workbook.Sheets[sheetName];

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const xlsxData: any[] = XLSX.utils.sheet_to_json(sheet, { defval: '' }); // Set default value for empty cells

  await Promise.all(
    xlsxData.map(async row => {
      const cleanedRow: WithdrawRequestRow = {
        id: row['Request ID']?.toString().trim() ?? '',
        status: row['Status']?.toString().trim() ?? '',
        amount: row['Amount']?.toString().trim() ?? '',
        payment_identifier: row['Payment Identifier']?.toString().trim() ?? '',
        transaction_type: row['Transaction Type']?.toString().trim() ?? '',
      };

      if (
        !cleanedRow.id ||
        !cleanedRow.status ||
        !cleanedRow.amount ||
        !cleanedRow.payment_identifier ||
        !cleanedRow.transaction_type
      ) {
        failedRows.push({ row: cleanedRow, error: 'Missing required fields' });
        return null; // Skip invalid rows
      }

      if (!Object.values(WithdrawRequestStatus).includes(cleanedRow.status as WithdrawRequestStatus)) {
        failedRows.push({ row: cleanedRow, error: 'Invalid status' });
      }

      const transaction = await sequelize.transaction();
      try {
        const request = await WithdrawRequest.findByPk(cleanedRow.id, { transaction });

        if (!request) {
          failedRows.push({ row: cleanedRow, error: 'Request not found' });
          await transaction.rollback();
          return;
        }
        console.log({ request });
        await request.update(
          {
            status: cleanedRow.status,
            processed_date: new Date(),
          },
          { transaction },
        );

        const [updatedCount] = await Plumber.update(
          {
            instant_withdrawal: 0,
            withdraw_money: 0,
          },
          {
            where: { user_id: request.requestor_id },
            transaction,
          },
        );
        console.log({ updatedCount, id: request.requestor_id });
        if (updatedCount === 0) {
          failedRows.push({ row: cleanedRow, error: 'User update failed' });
          await transaction.rollback();
          return;
        }

        await transaction.commit();
        console.log(`Processed successfully: ${cleanedRow.id}`);
        return cleanedRow;
      } catch (error) {
        await transaction.rollback();
        if (error instanceof Error) {
          failedRows.push({ row: cleanedRow, error: error.message });
        } else {
          failedRows.push({ row: cleanedRow, error: 'An unknown error occurred' });
        }
      }

      return null;
    }),
  );

  if (failedRows.length > 0) {
    return res.status(200).json({
      message: 'Some rows processed successfully, but there were errors',
      failedRows,
    });
  }

  res.status(200).json({
    message: 'XLSX processed successfully, no failed rows',
  });
}, 'Failed to upload withdraw requests');
