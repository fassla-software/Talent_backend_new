import multer from 'multer';
import express from 'express';
import {
  addWithdrawRequestHandler,
  updateWithdrawRequestHandler,
  getWithdrawRequestHandler,
  downloadWithdrawRequestHandler,
  uploadWithdrawRequestHandler,
  getUserWithdrawRequestHandler,
} from './withdraw.controllers';
import { authenticate } from '../../middlewares/auth.middleware';
import { addRequestValidator, updateRequestValidator } from './withdraw.validation';

const upload = multer();
const router = express.Router();

router.get('/', getWithdrawRequestHandler);
router.get('/my', authenticate, getUserWithdrawRequestHandler);
router.get('/download', downloadWithdrawRequestHandler);
router.post('/', authenticate, addRequestValidator, addWithdrawRequestHandler);
router.put('/:id', authenticate, updateRequestValidator, updateWithdrawRequestHandler);

router.post('/upload', upload.single('file'), uploadWithdrawRequestHandler);

export default router;
