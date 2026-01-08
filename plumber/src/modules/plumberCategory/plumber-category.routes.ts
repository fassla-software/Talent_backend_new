import express from 'express';
import {
  addCategoryPlumbersHandler,
  getPlumberCategoriesHandler,
  updatePlumberCategoryHandler,
  deletePlumberCategoryHandler,
  getAllCategoriesHandler,
  getPlumberSubcategoryOfCategoryHandler,
  getTreeCategoriesHandler,
  addProductCategoryPlumbersHandler,
} from './plumber-category.controller';
import { authenticate } from '../../middlewares/auth.middleware';
import {
  addPlumberCategoryValidator,
  paramsValidator,
  updatePlumberCategoryValidator,
} from './plumber-category.validation';
const router = express.Router();

router.get('/', getPlumberCategoriesHandler);
router.get('/tree', getTreeCategoriesHandler);
router.get('/all', getAllCategoriesHandler);
router.get('/:id', paramsValidator, getPlumberSubcategoryOfCategoryHandler);

router.post('/', addPlumberCategoryValidator, addCategoryPlumbersHandler);
router.post('/product', addPlumberCategoryValidator, addProductCategoryPlumbersHandler);
router.put('/:id', updatePlumberCategoryValidator, updatePlumberCategoryHandler);
router.delete('/:id', paramsValidator, deletePlumberCategoryHandler);

export default router;
