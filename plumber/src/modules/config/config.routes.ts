// user routes
import express from 'express';
import { getConfigHandler, updateConfigHandler, getConfigsHandler } from './config.controller';

const router = express.Router();

router.get('/all', getConfigsHandler);
router.get('/', getConfigHandler);
router.post('/', updateConfigHandler);

export default router;
