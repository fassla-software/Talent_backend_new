// user routes
import express from 'express';
import { getContactUsHandler, sendContactUsHandler } from './contactus.controller';
import { sendMessageVal } from './contactus.validation';

const router = express.Router();

router.get('/', getContactUsHandler);
router.post('/', sendMessageVal, sendContactUsHandler);

export default router;
