import { Router } from 'express';
import ReportDropdownController from './report-dropdowns.controller';

const router = Router();

router.get('/types', ReportDropdownController.getAllDropdownTypes);
router.get('/:type', ReportDropdownController.getDropdownOptions);
router.post('/', ReportDropdownController.createOption);
router.put('/:id', ReportDropdownController.updateOption);
router.delete('/:id', ReportDropdownController.deleteOption);

export default router;