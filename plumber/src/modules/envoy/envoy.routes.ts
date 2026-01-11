import { Router } from 'express';
import * as envoyController from './envoy.controller';

const router = Router();

router.get('/settings/:userId', envoyController.getEnvoySettingHandler);

export default router;
