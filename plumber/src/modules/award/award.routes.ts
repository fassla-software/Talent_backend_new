import { Router } from 'express';
import awardController from './award.controller';
import { authenticate, authorize } from '../../middlewares/auth.middleware';
import {
    createAwardVal,
    updateAwardVal,
    assignAwardVal,
    updateEnvoyAwardVal,
    paramsValidator,
} from './award.validation';

const router = Router();

router.post('/', createAwardVal, awardController.createAward);
router.get('/', awardController.getAwards);

router.get('/my-awards', authenticate, authorize('envoy'), awardController.getMyAwards);
router.get('/envoys/list', awardController.getEnvoys);

router.post('/assign', assignAwardVal, awardController.assignAwardToEnvoy);
router.get('/envoy-awards', awardController.getEnvoyAwards);
router.get('/envoy-awards/:id', paramsValidator, awardController.getEnvoyAwardById);
router.put('/envoy-awards/:id', updateEnvoyAwardVal, awardController.updateEnvoyAward);
router.delete('/envoy-awards/:id', paramsValidator, awardController.deleteEnvoyAward);

router.get('/:id', paramsValidator, awardController.getAwardById);
router.put('/:id', updateAwardVal, awardController.updateAward);
router.delete('/:id', paramsValidator, awardController.deleteAward);


export default router;
