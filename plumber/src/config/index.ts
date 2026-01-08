import { loadConfig, getConfig } from 'dotenv-handler';
import path from 'path';
const mode = process.env.NODE_ENV || 'development';
const envPath = path.resolve(__dirname, `../../.env.${mode}`);

export default (): void => {
  loadConfig(envPath, {
    required: [
      'PORT',
      'KEY',
      'BASE_URL',
      'DB_DATABASE',
      'DB_USERNAME',
      'DB_HOST',
      'DB_PORT',
      'SMS_MISR_URL',
      'SMS_MISR_USERNAME',
      'SMS_MISR_PASSWORD',
      'SMS_MISR_SENDER',
      'TZ',
    ],
    defaults: {
      PORT: '8587',
      DB_PASSWORD: '',
    },
    expand: true,
  });
  process.env.TZ = getConfig('TZ');
};
