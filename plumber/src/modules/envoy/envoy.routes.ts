import { Router } from 'express';
import * as envoyController from './envoy.controller';
import { authenticate } from '../../middlewares/auth.middleware';
import { registerUserByEnvoyValidation, createNoteValidation } from './envoy.validation';
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

router.get(
    '/stats',
    authenticate,
    envoyController.getEnvoyStatisticsHandler
);

router.post(
    '/admin/stats',
    envoyController.getAdminEnvoyStatisticsHandler
);

router.get(
    '/notifications',
    authenticate,
    envoyController.getNotificationsHandler
);

router.post(
    '/notes',
    authenticate,
    createNoteValidation,
    envoyController.createNoteHandler
);

router.get(
    '/clients',
    authenticate,
    envoyController.getEnvoyClientsHandler
);

export default router;
