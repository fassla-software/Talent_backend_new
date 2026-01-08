import express from 'express';
import {
    getTradersHandler,
    getTraderByIdHandler,
    updateTraderHandler,
    updateProfileHandler,
    getProfileHandler,
    bulkDeleteTradersHandler,
    acceptTraderHandler,
    rejectTraderHandler
} from './trader.controller';
import { paramsValidator } from '../plumber/plumber.validation';
import { bulkDeleteValidation } from './trader.validation';
import { authenticate } from '../../middlewares/auth.middleware';

const router = express.Router();

router.get('/', getTradersHandler);
router.get('/profile', authenticate, getProfileHandler);
router.get('/:id', paramsValidator, getTraderByIdHandler);
router.put('/profile', authenticate, updateProfileHandler);
router.put('/:id', paramsValidator, updateTraderHandler);
router.put('/:id/accept', paramsValidator, acceptTraderHandler);
router.put('/:id/reject', paramsValidator, rejectTraderHandler);

//admin
router.delete('/bulk-delete', bulkDeleteValidation, bulkDeleteTradersHandler);

export default router;
