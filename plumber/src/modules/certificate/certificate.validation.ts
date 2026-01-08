import { query } from 'express-validator';
import { strict, handleValidationErrors } from '../../utils/base.validators';

export const filterVal = [
  query('phone')
    .custom(value => {
      return value.length === 11;
    })
    .optional()
    .withMessage('Invalid phone number'),
  query('nationality_id').isString().optional().withMessage('nationality id must be string'),
  query('limit').isInt().optional().withMessage(`limit must be integer`),
  query('skip').isInt().optional().withMessage(`skip must be integer`),
  handleValidationErrors,
  strict,
];
