import { Router } from 'express';
import * as envoyController from './envoy.controller';
import { authenticate } from '../../middlewares/auth.middleware';
import { registerUserByEnvoyValidation } from './envoy.validation';
import upload from '../../middlewares/upload.middleware';

const router = Router();

router.get('/settings/:userId', envoyController.getEnvoySettingHandler);
router.post(
    '/register-user',
    authenticate,
    upload.array('images', 2), // Handle up to 2 nationality images
    registerUserByEnvoyValidation,
    envoyController.registerUserByEnvoyHandler
);

export default router;
