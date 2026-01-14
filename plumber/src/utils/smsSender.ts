import axios from 'axios';
import { getConfig } from 'dotenv-handler';

const SMS_MISR_URL = getConfig('SMS_MISR_URL')!;
const SMS_MISR_USERNAME = getConfig('SMS_MISR_USERNAME')!;
const SMS_MISR_PASSWORD = getConfig('SMS_MISR_PASSWORD')!;
const SMS_MISR_SENDER = getConfig('SMS_MISR_SENDER')!;
const SMS_MISR_TEMPLATE = getConfig('SMS_MISR_TEMPLATE')!;

if (!SMS_MISR_URL || !SMS_MISR_USERNAME || !SMS_MISR_PASSWORD || !SMS_MISR_SENDER || !SMS_MISR_TEMPLATE) {
  throw new Error('Missing one or more SMS Misr configuration values');
}

const baseUrl = new URL(SMS_MISR_URL);

class SMSSender {
  private formatPhoneNumber(mobile: string): string {
    const cleanedMobile = mobile.replace(/\D/g, '');

    if (cleanedMobile.startsWith('0')) {
      const formattedMobile = `20${cleanedMobile.slice(1)}`;
      if (formattedMobile.length === 12) return formattedMobile;
    } else if (cleanedMobile.startsWith('20') && cleanedMobile.length === 12) {
      return cleanedMobile;
    }

    // Throw an error if the number is invalid
    throw new Error('Invalid phone number format. Expected format: 20XXXXXXXXXX.');
  }

  async sendSMS(mobile: string, otp: string) {
    try {
      const formattedMobile = this.formatPhoneNumber(mobile);

      const url = new URL(baseUrl.toString());
      url.pathname = '/api/OTP/';
      url.searchParams.append('environment', '1'); // 2 to test mode  1 for live
      url.searchParams.append('username', SMS_MISR_USERNAME);
      url.searchParams.append('password', SMS_MISR_PASSWORD);
      url.searchParams.append('sender', SMS_MISR_SENDER);
      url.searchParams.append('mobile', formattedMobile);
      url.searchParams.append('template', SMS_MISR_TEMPLATE);
      url.searchParams.append('otp', otp);

      console.log('Sending SMS to:', formattedMobile);
      const response = await axios.post(url.toString());
      return response.data;
    } catch (error) {
      console.error('Error sending SMS:', error);
      throw error;
    }
  }

  async sendMessage(mobile: string, message: string) {
    try {
      const formattedMobile = this.formatPhoneNumber(mobile);

      const url = new URL(baseUrl.toString());
      url.pathname = '/api/SMS/';
      url.searchParams.append('environment', '1'); // 2 to test mode  1 for live
      url.searchParams.append('username', SMS_MISR_USERNAME);
      url.searchParams.append('password', SMS_MISR_PASSWORD);
      url.searchParams.append('sender', SMS_MISR_SENDER);
      url.searchParams.append('mobile', formattedMobile);
      url.searchParams.append('message', message);
      url.searchParams.append('language', '1'); // 1 for English

      console.log('Sending Message to:', formattedMobile);
      const response = await axios.post(url.toString());
      return response.data;
    } catch (error) {
      console.error('Error sending Message:', error);
      throw error;
    }
  }

  async checkBalance() {
    try {
      const url = new URL(baseUrl.toString());
      url.pathname = '/api/Balance/';
      url.searchParams.append('username', SMS_MISR_USERNAME);
      url.searchParams.append('password', SMS_MISR_PASSWORD);

      console.log('Checking balance using URL:', url.toString());
      const response = await axios.post(url.toString());
      return response.data;
    } catch (error) {
      console.error('Error checking balance:', error);
      throw error;
    }
  }
}

export default SMSSender;
