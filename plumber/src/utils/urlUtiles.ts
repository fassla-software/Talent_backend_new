import { getConfig } from 'dotenv-handler';

export const shortenUrl = (baseUrl: string) => {
  const shortenedUrl = `${baseUrl}.pdf`;
  return shortenedUrl;
};

const BASE_URL = getConfig('BASE_URL');
export const viewUrl = (name: string) => {
  const shortenedUrl = `${BASE_URL}/PDF/${name}`;
  return shortenedUrl;
};
