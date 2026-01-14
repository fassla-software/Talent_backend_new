import express from 'express';
import connectDB from './config/db';
import { errorHandler } from './middlewares/errorHandler.middleware';
import { getConfig } from 'dotenv-handler';
import config from './config';
config();

// routes
import indexRouter from './routers/index.router';
// middlewares
import cors from 'cors';
import helmet from 'helmet';
import rateLimit from 'express-rate-limit';
import morgan from 'morgan';
import { initTraderStatusCron } from './cron/trader-status.cron';

const PORT = getConfig('PORT');

const app = express();

const limiter = rateLimit({
  windowMs: 60 * 1000,
  max: 100,
});

app.use(limiter);
app.use(cors());
app.use(helmet());
app.use(
  morgan(':method :url :status :response-time ms - :date[web]', {
    // skip: (req, res) => res.statusCode < 300,
  }),
);
app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use('/PDF', express.static('PDF'));
app.use('/uploads', express.static('uploads'));
app.set('trust proxy', 1);

app.get('/', (req, res) => {
  res.send('Hello World!');
});

app.use(indexRouter);

// not fount route
app.use((req, res) => {
  res.status(404).json({ message: 'Route not found' });
});

app.use(errorHandler);

connectDB
  .sync({ force: false })
  .then(() => {
    console.log('Database synced');
  })
  .catch(err => {
    console.error('Error syncing database:', err);
  });
app.listen(PORT, () => {
  console.log(`Server is running on http://localhost:${PORT}`);
  initTraderStatusCron();
});
