import express from 'express';
import {
  registerHandler,
  loginHandler,
  verifyPlumberPhoneHandler,
  resendOtp,
  completeRegisterPlumberReHandler,
  acceptPlumberHandler,
  rejectPlumberHandler,
  updatePlumberHandler,
  getPlumbersHandler,
  getPlumberPointsHandler,
  getProfileHandler,
  getPlumberInfoHandler,
  getPlumbersInfoHandler,
  downloadPlumberInfoHandler,
  getPlumberReportHandler,
  downloadPlumberReportHandler,
  getPlumberWithdrawHandler,
  getPlumberByIdHandler,
  forgetPassword,
  validatePhoneOtpAndGenerateToken,
  updateUserPasswordHandler,
  getPlumberListHandler,
  addReferralPointsHandler,
  pointsForRegestrationHandler,
  refreshTokenHandler,

} from './plumber.controller';
import { checkPhoneUnique ,checkReferralCode } from './plumber.middleware';
import upload from '../../middlewares/upload.middleware';
import { authenticate, verifyShortLiveToken } from '../../middlewares/auth.middleware';
import {
  completeRegisterPlumberValidator,
  forgetPasswordValidator,
  loginValidator,
  newPasswordValidator,
  paramsValidator,
  registerValidator,
  resendOtpValidator,
  updatedPlumberValidator,
  validatePhoneOtpValidator,
  verifyValidator,
} from './plumber.validation';


const router = express.Router();

router.post('/register', registerValidator, checkPhoneUnique, registerHandler);
router.post('/login', loginValidator, loginHandler);
router.post('/refresh-token', refreshTokenHandler);




router.get('/getPlumberList',authenticate,getPlumberListHandler);
router.put(
  '/complete-register',
  authenticate,
  upload.array('images', 2),
  completeRegisterPlumberValidator,
  completeRegisterPlumberReHandler,
);

router.get('/', getPlumbersHandler);

router.get('/points', authenticate, getPlumberPointsHandler);
router.get('/withdraw', authenticate, getPlumberWithdrawHandler);
router.get('/profile', authenticate, getProfileHandler);

router.get('/info', getPlumbersInfoHandler);


router.get('/:id', paramsValidator, getPlumberByIdHandler);
router.get('/info/:id', paramsValidator, getPlumberInfoHandler);
router.get('/info/:id/download', paramsValidator, downloadPlumberInfoHandler);
router.get('/report/:id', paramsValidator, getPlumberReportHandler);
router.get('/report/:id/download', paramsValidator, downloadPlumberReportHandler);

router.put('/profile', authenticate, updatedPlumberValidator, updatePlumberHandler);

router.post('/verify', verifyValidator, verifyPlumberPhoneHandler);
router.post('/resend-otp', resendOtpValidator, resendOtp);

router.post('/forget-password', forgetPasswordValidator, forgetPassword);
router.post('/validate-otp', validatePhoneOtpValidator, validatePhoneOtpAndGenerateToken);
router.put('/newpassword', verifyShortLiveToken, newPasswordValidator, updateUserPasswordHandler);


// dashboard
router.put('/:id/accept', paramsValidator, acceptPlumberHandler);
router.put('/:id/reject', paramsValidator, rejectPlumberHandler);
router.post('/addReferralPoints',  addReferralPointsHandler);
router.post('/pointsForRegestration',  pointsForRegestrationHandler);


export default router;
