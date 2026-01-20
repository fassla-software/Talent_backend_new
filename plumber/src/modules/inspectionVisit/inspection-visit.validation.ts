import { body, param } from 'express-validator';
import { strict, handleValidationErrors } from '../../utils/base.validators';

export const checkInValidation = [
  body('latitude')
    .isFloat({ min: -90, max: 90 })
    .withMessage('Latitude must be a valid decimal number between -90 and 90')
    .notEmpty()
    .withMessage('latitude is required'),
  body('longitude')
    .isFloat({ min: -180, max: 180 })
    .withMessage('Longitude must be a valid decimal number between -180 and 180')
    .notEmpty()
    .withMessage('longitude is required'),
  body('trader_id')
    .optional()
    .isInt()
    .withMessage('trader_id must be a number'),
  body('plumber_id')
    .optional()
    .isInt()
    .withMessage('plumber_id must be a number'),
  body()
    .custom((value, { req }) => {
      if (!req.body.trader_id && !req.body.plumber_id) {
        throw new Error('Either trader_id or plumber_id is required');
      }
      if (req.body.trader_id && req.body.plumber_id) {
        throw new Error('Cannot provide both trader_id and plumber_id');
      }
      return true;
    }),
  handleValidationErrors,
  strict,
];

export const checkOutValidation = [
  body('inspection_visit_id')
    .isInt()
    .withMessage('inspection_visit_id must be a number')
    .notEmpty()
    .withMessage('inspection_visit_id is required'),
  body('latitude')
    .isFloat({ min: -90, max: 90 })
    .withMessage('Latitude must be a valid decimal number between -90 and 90')
    .notEmpty()
    .withMessage('latitude is required'),
  body('longitude')
    .isFloat({ min: -180, max: 180 })
    .withMessage('Longitude must be a valid decimal number between -180 and 180')
    .notEmpty()
    .withMessage('longitude is required'),
  handleValidationErrors,
  strict,
];

export const submitVisitReportValidation = [
  body('inspection_visit_id')
    .isInt()
    .withMessage('inspection_visit_id must be a number')
    .notEmpty()
    .withMessage('inspection_visit_id is required'),
  // Customer Information
  // customer_name and phone are optional if trader_id exists (will be fetched from trader)
  body('customer_name')
    .optional()
    .isString()
    .withMessage('customer_name must be a string'),
  // company_name, location, and email are optional if trader_id or plumber_id exists (will be fetched from trader/plumber)
  body('company_name').optional().isString().withMessage('company_name must be a string'),
  body('location')
    .optional()
    .isString()
    .withMessage('location must be a string'),
  body('region_province').optional().isString().withMessage('region_province must be a string'),
  body('phone')
    .optional()
    .isString()
    .withMessage('phone must be a string'),
  // Validate that either trader_id/plumber_id exists OR customer_name, phone, and location are provided
  body()
    .custom((value, { req }) => {
      // If trader_id or plumber_id exists in the visit, customer_name, phone, location, email, and company_name are not required
      // (they will be fetched from trader/plumber data)
      // This validation will be handled in the service
      return true;
    }),
  body('email')
    .optional({ nullable: true, checkFalsy: true })
    .custom((value) => {
      // Allow empty string, null, or undefined
      if (value === null || value === undefined || value === '') {
        return true;
      }
      // If provided, must be a valid email
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(value)) {
        throw new Error('email must be a valid email address');
      }
      return true;
    }),
  body('client_type').optional().isString().withMessage('client_type must be a string'),
  body('visit_type').optional().isString().withMessage('visit_type must be a string'),
  // Visit Details
  body('visit_result')
    .isString()
    .withMessage('visit_result must be a string')
    .notEmpty()
    .withMessage('visit_result is required'),
  body('interest_level').optional().isString().withMessage('interest_level must be a string'),
  body('purchase_readiness').optional().isString().withMessage('purchase_readiness must be a string'),
  body('authority_level').optional().isString().withMessage('authority_level must be a string'),
  body('sales_value').optional().isFloat({ min: 0 }).withMessage('sales_value must be a non-negative number'),
  body('planned_purchase_date')
    .optional()
    .custom((value, { req }) => {
      if (!value) return true; // Allow empty/undefined

      // Accept format: M/D/YYYY (month/day/year)
      const datePattern = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/;
      const match = String(value).match(datePattern);

      if (!match) {
        throw new Error('planned_purchase_date must be in format M/D/YYYY (month/day/year)');
      }

      const month = parseInt(match[1], 10);
      const day = parseInt(match[2], 10);
      const year = parseInt(match[3], 10);

      // Validate month (1-12)
      if (month < 1 || month > 12) {
        throw new Error('Month must be between 1 and 12');
      }

      // Validate day (1-31, basic check)
      if (day < 1 || day > 31) {
        throw new Error('Day must be between 1 and 31');
      }

      // Validate year (reasonable range)
      if (year < 1900 || year > 2100) {
        throw new Error('Year must be between 1900 and 2100');
      }

      // Create date object to validate actual date (handles invalid dates like 2/30/2026)
      const date = new Date(year, month - 1, day);
      if (date.getFullYear() !== year || date.getMonth() !== month - 1 || date.getDate() !== day) {
        throw new Error('Invalid date');
      }

      // Convert to ISO8601 format (YYYY-MM-DD) and update request body
      const monthStr = String(month).padStart(2, '0');
      const dayStr = String(day).padStart(2, '0');
      const isoDate = `${year}-${monthStr}-${dayStr}`;
      req.body.planned_purchase_date = isoDate;

      return true;
    }),
  body('outcome_classification').optional().isString().withMessage('outcome_classification must be a string'),
  body('next_action').optional().isString().withMessage('next_action must be a string'),
  // Sales Classification
  body('sales_classification').optional().isString().withMessage('sales_classification must be a string'),
  // Additional
  body('notes').optional().isString().withMessage('notes must be a string'),
  body('images')
    .optional()
    .isArray()
    .withMessage('images must be an array')
    .custom(images => {
      if (images && images.length > 10) {
        throw new Error('Maximum 10 images allowed');
      }
      return true;
    }),
  body('images.*')
    .optional()
    .isURL({ require_tld: false })
    .withMessage('Each image must be a valid URL'),
  handleValidationErrors,
  strict,
];

export const getVisitStatusValidation = [
  param('id')
    .isInt()
    .withMessage('trader_id must be a number')
    .notEmpty()
    .withMessage('trader_id is required'),
  handleValidationErrors,
  strict,
];
