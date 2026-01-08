import { Sequelize } from 'sequelize-typescript';
import { getConfig } from 'dotenv-handler';
import config from '../config';
config();

const DB_DATABASE = getConfig('DB_DATABASE') as string;
const DB_USERNAME = getConfig('DB_USERNAME') as string;
const DB_PASSWORD = getConfig('DB_PASSWORD') as string;
const DB_HOST = getConfig('DB_HOST') as string;
const DB_PORT = getConfig('DB_PORT') as string;

const sequelize = new Sequelize(DB_DATABASE, DB_USERNAME, DB_PASSWORD, {
  host: DB_HOST,
  port: Number(DB_PORT) || 3306,
  dialect: 'mysql',
  timezone: '+02:00', // Africa/Cairo is UTC+2
});

export default sequelize;
