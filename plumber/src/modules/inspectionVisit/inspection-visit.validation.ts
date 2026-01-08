import { body, param } from 'express-validator';
import { strict, handleValidationErrors } from '../../utils/base.validators';

export const checkInValidation = [
  body('inspection_request_id')
    .isInt()
    .withMessage('inspection_request_id must be a number')
    .notEmpty()
    .withMessage('inspection_request_id is required'),
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

export const checkOutValidation = [
  body('inspection_request_id')
    .isInt()
    .withMessage('inspection_request_id must be a number')
    .notEmpty()
    .withMessage('inspection_request_id is required'),
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
  body('inspection_request_id')
    .isInt()
    .withMessage('inspection_request_id must be a number')
    .notEmpty()
    .withMessage('inspection_request_id is required'),
  // Customer Information
  body('customer_name')
    .isString()
    .withMessage('customer_name must be a string')
    .notEmpty()
    .withMessage('customer_name is required'),
  body('company_name').optional().isString().withMessage('company_name must be a string'),
  body('location')
    .isString()
    .withMessage('location must be a string')
    .notEmpty()
    .withMessage('location is required'),
  body('region_province').optional().isString().withMessage('region_province must be a string'),
  body('phone')
    .isString()
    .withMessage('phone must be a string')
    .notEmpty()
    .withMessage('phone is required'),
  body('email').optional().isEmail().withMessage('email must be a valid email address'),
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
    .withMessage('inspection_request_id must be a number')
    .notEmpty()
    .withMessage('inspection_request_id is required'),
  handleValidationErrors,
  strict,
];