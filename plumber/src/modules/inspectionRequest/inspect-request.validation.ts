import { body, param, query } from 'express-validator';
import { strict, handleValidationErrors } from '../../utils/base.validators';
import { RequestStatus } from './inspection_request.model';
import { RequestFilterStatus } from './dto/request-filter-status.dto';

export const addInspectionRequestVal = [
  body('user_name')
    .notEmpty()
    .withMessage('user name is required')
    .isString()
    .withMessage('user name must be a string'),
  body('inspection_date').isISO8601().withMessage('Inspection date must be a valid date'),
  body('user_phone')
    .notEmpty()
    .withMessage('user phone is required')
    .custom(value => {
      return value.length === 11;
    })
    .withMessage('user phone must be a valid phone number'),
  body('nationality_id').isString().withMessage('nationality id must be a string'),
  body('city').notEmpty().withMessage('City is required').isString().withMessage('City must be a string'),
  body('area').notEmpty().withMessage('Area is required').isString().withMessage('Area must be a string'),
  body('address').notEmpty().withMessage('Address is required').isString().withMessage('Address must be a string'),
  body('seller_name').isString().withMessage('Seller name must be a string'),
  body('seller_phone')
    .custom(value => {
      return value.length === 11 || value.length === 0;
    })
    .withMessage('Seller phone must be a valid phone number'),
  body('items')
    .isArray({ min: 1 })
    .withMessage('Items must be a non-empty array')
    .custom(items => {
      if (!Array.isArray(items)) {
        throw new Error('Items must be an array');
      }

      for (const item of items) {
        if (typeof item !== 'object' || item === null) {
          throw new Error('Each item must be an object');
        }
        if (!item.subcategory_id || typeof item.subcategory_id !== 'number') {
          throw new Error('Each item must have a valid subcategory_id (number)');
        }
        if (item.count === undefined || typeof item.count !== 'number' || item.count < 0) {
          throw new Error('Each item must have a valid count (non-negative number)');
        }
      }

      return true;
    }),
  body('certificate_id')
    .notEmpty()
    .withMessage('Certificate ID is required')
    .isString()
    .withMessage('Certificate ID must be a string'),
  body('images')
    .optional()
    .isArray()
    .withMessage('Images must be an array')
    .custom(images => {
      if (images && images.length > 10) {
        throw new Error('Maximum 10 images allowed');
      }
      return true;
    }),
  body('status')
    .optional()
    .isIn([RequestStatus.PENDING, RequestStatus.SEND])
    .withMessage(`Status must be one of ${RequestStatus.PENDING}, or ${RequestStatus.SEND}`),
  body('description').isString().withMessage('Description must be a string').optional(),
  body('user_lat')
    .isFloat({ min: -90, max: 90 })
    .withMessage('User latitude must be a valid decimal number between -90 and 90'),
  body('user_long')
    .isFloat({ min: -180, max: 180 })
    .withMessage('User longitude must be a valid decimal number between -180 and 180'),
  body('plumber_id')
    .optional({ nullable: true })
    .custom(value => {
      if (value === null) return true;
      if (typeof value === 'string') return true;
      throw new Error('plumber_id must be a string or null');
    }),

  handleValidationErrors,
  strict,
];

export const paramsValidator = [
  param('id').isInt().withMessage('request id must be number'),
  handleValidationErrors,
  strict,
];

export const assignRequestVal = [
  body('request_id').isInt().withMessage('request id must be number'),
  body('inspector_id').isInt().withMessage('inspector id must be number'),
  handleValidationErrors,
  strict,
];

export const checkRequestVal = [
  body('request_id').isInt().withMessage('request id must be number'),
  body('description').isString().withMessage('Description must be a string').optional(),
  body('inspection_images').isArray().withMessage('inspection images must be an array'),
  body('inspection_images.*').isURL({ require_tld: false }).withMessage('Image must be an url'),
  body('inspection_lat')
    .isFloat({ min: -90, max: 90 })
    .withMessage('Inspection latitude must be a valid decimal number between -90 and 90'),
  body('inspection_long')
    .isFloat({ min: -180, max: 180 })
    .withMessage('Inspection longitude must be a valid decimal number between -180 and 180'),
  body('items')
    .optional()
    .isArray({ min: 1 })
    .withMessage('Items must be a non-empty array')
    .custom(items => {
      if (!Array.isArray(items)) {
        throw new Error('Items must be an array');
      }

      for (const item of items) {
        if (typeof item !== 'object' || item === null) {
          throw new Error('Each item must be an object');
        }
        if (!item.subcategory_id || typeof item.subcategory_id !== 'number') {
          throw new Error('Each item must have a valid subcategory_id (number)');
        }
        if (item.count === undefined || typeof item.count !== 'number' || item.count < 0) {
          throw new Error('Each item must have a valid count (non-negative number)');
        }
      }

      return true;
    }),
  body('request_status')
    .isIn([RequestStatus.ACCEPTED, RequestStatus.REJECTED])
    .withMessage(`Status must be one of ${RequestStatus.ACCEPTED}, or ${RequestStatus.REJECTED}`),
  body('comment').isString().withMessage('comment must be a string').optional(),
  handleValidationErrors,
  strict,
];

export const filterVal = [
  query('user_name').isString().withMessage('user name must be string').optional(),
  query('plumber_name').isString().withMessage('plumber name must be string').optional(),
  query('city').isString().withMessage('city must be string').optional(),
  query('area').isString().withMessage('area must be string').optional(),
  query('status')
    .isIn(Object.values(RequestFilterStatus))
    .optional()
    .withMessage(`Status must be one of ${Object.values(RequestFilterStatus).join(', ')}`),
  query('limit').isInt().optional().withMessage(`limit must be integer`),
  query('skip').isInt().optional().withMessage(`skip must be integer`),
  handleValidationErrors,
  strict,
];

export const approveRequestVal = [
  body('request_id').isInt().withMessage('request id must be number'),
  body('request_status')
    .isIn([RequestStatus.APPROVED, RequestStatus.CANCELLED])
    .withMessage(`Status must be one of ${RequestStatus.APPROVED}, or ${RequestStatus.CANCELLED}`),
  handleValidationErrors,
  strict,
];

export const bulkDeleteValidation = [
  body('ids')
    .isArray({ min: 1 })
    .withMessage('IDs must be a non-empty array')
    .custom(ids => {
      if (!Array.isArray(ids)) {
        throw new Error('IDs must be an array');
      }
      for (const id of ids) {
        if (typeof id !== 'number' || !Number.isInteger(id)) {
          throw new Error('Each ID must be an integer');
        }
      }
      return true;
    }),
  handleValidationErrors,
  strict,
];

export const pendingRequestVal = [
  body('request_id').isInt().withMessage('request id must be number'),
  body('note')
    .notEmpty()
    .withMessage('note is required')
    .isString()
    .withMessage('note must be a string'),
  handleValidationErrors,
  strict,
];
