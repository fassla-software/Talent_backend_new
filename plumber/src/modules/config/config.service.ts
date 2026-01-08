import HttpError from '../../utils/HttpError';
import AppConfig from './config.model';

// Create or update a configuration
export async function setConfigService(data: { key: string; value: string }) {
  const { key, value } = data;
  const [config, created] = await AppConfig.findOrCreate({
    where: { key },
    defaults: { value },
  });

  if (!created) {
    config.value = value;
    await config.save();
  }

  return config;
}

// Retrieve a configuration by key
export async function getConfigService(key: string) {
  const config = await AppConfig.findOne({ where: { key } });
  if (!config) {
    throw new HttpError(`Configuration for ${key} not found`, 500);
  }
  return config.value;
}

export async function getallConfigService() {
  const config = await AppConfig.findAll();
  return config;
}
