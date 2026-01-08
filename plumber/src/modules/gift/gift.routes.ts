import express from 'express';
import { authenticate } from '../../middlewares/auth.middleware';
import { getAllGiftsHandler, addGiftHandler, deleteGiftHandler, updateGiftHandler,getGiftsHandler } from './gift.controller';
import { addGiftVal, paramsValidator, updateGiftVal } from './gift.validation';
const router = express.Router();

router.get('/all', getGiftsHandler);
router.get('/',authenticate, getAllGiftsHandler);
router.post('/', addGiftVal, addGiftHandler);
router.put('/:id', updateGiftVal, updateGiftHandler);
router.delete('/:id', paramsValidator, deleteGiftHandler);

export default router;
