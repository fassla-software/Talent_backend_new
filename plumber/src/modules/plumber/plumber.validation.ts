import { body, param } from 'express-validator';
import { strict, handleValidationErrors } from '../../utils/base.validators';

export const registerValidator = [
  body('phone')
    .custom(value => {
      return value.length === 11;
    })
    .withMessage('Invalid phone number'),
  body('password').isString().withMessage('Password must be string'),
  body('name').isString().withMessage('Name must be a string'),
  body('city').isString().withMessage('City must be a string'),
  body('area').isString().withMessage('Area must be a string'),
  body('role')
    .optional()
    .isIn(['plumber', 'trader'])
    .withMessage('Role must be either plumber or trader'),
  body('referralCode')
    .optional()
    .isString()
    .withMessage('Referral code must be a string'),
  handleValidationErrors,
  strict,
];

export const newPasswordValidator = [
  body('password').isString().withMessage('Password must be string'),
  handleValidationErrors,
  strict,
];

export const updatedPlumberValidator = [
  body('phone')
    .custom(value => {
      return value.length === 11;
    })
    .withMessage('Invalid phone number')
    .optional(),
  body('image').isURL({ require_tld: false }).withMessage('Image must be a url').optional(),
  body('name').isString().withMessage('Name must be a string').optional(),
  body('area').isString().withMessage('Area must be a string').optional(),
  body('city').isString().withMessage('City must be a string').optional(),
  handleValidationErrors,
  strict,
];

export const forgetPasswordValidator = [
  body('phone')
    .custom(value => {
      return value.length === 11;
    })
    .withMessage('Invalid phone number'),
  body('role')
    .optional()
    .isIn(['plumber', 'trader', 'envoy'])
    .withMessage('Role must be plumber, trader or envoy'),
  handleValidationErrors,
  strict,
];

export const validatePhoneOtpValidator = [
  body('phone')
    .custom(value => {
      return value.length === 11;
    })
    .withMessage('Invalid phone number'),
  body('otp').isLength({ min: 4 }).withMessage('OTP must be at least 4 characters long'),
  body('role')
    .optional()
    .isIn(['plumber', 'trader', 'envoy'])
    .withMessage('Role must be plumber, trader or envoy'),
  handleValidationErrors,
  strict,
];

export const loginValidator = [
  body('phone')
    .custom(value => {
      return value.length === 11;
    })
    .withMessage('Invalid phone number'),
  body('password').isString().withMessage('Password must be a string'),
  handleValidationErrors,
  strict,
];

export const verifyValidator = [
  body('phone')
    .custom(value => {
      return value.length === 11;
    })
    .withMessage('Invalid phone number'),
  body('otp').isLength({ min: 4 }).withMessage('OTP must be at least 4 characters long'),
  body('role')
    .optional()
    .isIn(['plumber', 'trader', 'envoy'])
    .withMessage('Role must be plumber, trader or envoy'),
  handleValidationErrors,
  strict,
];

export const completeRegisterPlumberValidator = [
  body('nationality_id').isLength({ min: 14 }).withMessage('Nationality ID must be a string'),
  body('nationality_image1').isURL({ require_tld: false }).withMessage('nationality_image1 must be a string'),
  body('nationality_image2').isURL({ require_tld: false }).withMessage('nationality_image2 must be a string'),
  body('role')
    .isIn(['plumber', 'trader', 'envoy'])
    .withMessage('Role must be plumber, trader or envoy'),
  handleValidationErrors,
  strict,
];

export const resendOtpValidator = [
  body('phone')
    .custom(value => {
      return value.length === 11;
    })
    .withMessage('Invalid phone number'),
  body('role')
    .optional()
    .isIn(['plumber', 'trader', 'envoy'])
    .withMessage('Role must be plumber, trader or envoy'),
  handleValidationErrors,
  strict,
];

export const paramsValidator = [
  param('id').isInt().withMessage('plumber id must be number'),
  handleValidationErrors,
  strict,
];


