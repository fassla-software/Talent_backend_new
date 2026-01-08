import { body, param } from 'express-validator';
import { strict, handleValidationErrors } from '../../utils/base.validators';

export const addPlumberCategoryValidator = [
  body('name').isString().withMessage('Name must be a string'),
  body('image').isString().withMessage('Image must be a string'),
  body('category_id').isInt().withMessage('category_id must be a number').optional(),
  body('points').isInt().withMessage('points must be a number').optional(),
  handleValidationErrors,
  strict,
];

export const addProductPlumberCategoryValidator = [
  body('name').isString().withMessage('Name must be a string'),
  body('image').isString().withMessage('Image must be a string'),
  body('category_id').isInt().withMessage('category_id must be a number').optional(),
  body('points').isInt().withMessage('points must be a number'),
  body('product_flag').isBoolean().withMessage('productFlag must be a boolean'),
  handleValidationErrors,
  strict,
];

export const updatePlumberCategoryValidator = [
  param('id').isInt().withMessage('category id must be number'),
  body('name').isString().withMessage('Name must be a string').optional(),
  body('image').isString().withMessage('Image must be a string').optional(),
  body('category_id').isInt().withMessage('category_id must be a number').optional(),
  body('points').isInt().withMessage('points must be a number').optional(),
  handleValidationErrors,
  strict,
];

export const paramsValidator = [
  param('id').isInt().withMessage('category id must be number'),
  handleValidationErrors,
  strict,
];
