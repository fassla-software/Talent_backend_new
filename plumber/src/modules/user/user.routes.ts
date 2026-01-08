// user routes
import express from 'express';
import { getUsersHandler } from './user.controllers';

const router = express.Router();

router.get('/', getUsersHandler);

export default router;