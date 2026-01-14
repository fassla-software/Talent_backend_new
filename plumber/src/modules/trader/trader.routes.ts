import express from 'express';
import {
    getTradersHandler,
    getTraderByIdHandler,
    updateTraderHandler,
    updateProfileHandler,
    getProfileHandler,
    bulkDeleteTradersHandler,
    acceptTraderHandler,
    rejectTraderHandler,
    searchTradersHandler
} from './trader.controller';
import { paramsValidator } from '../plumber/plumber.validation';
import { bulkDeleteValidation, searchValidation } from './trader.validation';
import { authenticate, authorize } from '../../middlewares/auth.middleware';
import { Roles } from '../role/role.model';

const router = express.Router();

router.get('/search', authenticate, authorize(Roles.Envoy), searchValidation, searchTradersHandler);
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
