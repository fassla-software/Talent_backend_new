import express from 'express';
import { getCertificatesHandler } from './certificate.controller';
import { filterVal } from './certificate.validation';

const router = express.Router();

router.get('/', filterVal, getCertificatesHandler);

export default router;
