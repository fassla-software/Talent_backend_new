import puppeteer from 'puppeteer';
import path from 'path';
import { generateBarcode } from './generateBarcode';

type certificateData = {
  certificate_id: string;
  user_name: string;
  city: string;
  address: string;
  company_name: string;
  date: string;
  company_phone: string;
  user_phone: string;
  url: string;
  description: string;
};

// Function to generate a PDF with a background image and barcode
export const generateCardWithBackground = async (certificate: certificateData, shortenedUrl: string) => {
  const { user_name, certificate_id, city, company_name, date, company_phone, user_phone, address, description } =
    certificate;
  const templatePath = path.resolve(__dirname, '../../assets/index.html'); // Path to the HTML template

  try {
    const barcodeBase64 = await generateBarcode(shortenedUrl);
    const browser = await puppeteer.launch({
      headless: true, // Runs Chrome in headless mode
      args: ['--no-sandbox', '--disable-setuid-sandbox'], // For safer containerized environments
      defaultViewport: { width: 595, height: 842 }, // Set viewport to A4 size
    });
    const page = await browser.newPage();
    await page.setViewport({ width: 595, height: 842 });
    await page.goto(`file://${templatePath}`, { waitUntil: 'networkidle2' });

    await page.evaluate(
      (params: {
        user_name: string;
        certificate_id: string;
        city: string;
        address: string;
        barcodeBase64: string;
        company_name: string;
        date: string;
        company_phone: string;
        user_phone: string;
        description: string;
      }) => {
        const {
          user_name = '',
          certificate_id = '',
          city = '',
          address = '',
          barcodeBase64 = '',
          company_name = '',
          date = '',
          company_phone = '',
          user_phone = '',
          description = '',
        } = params;

        const nameElement = document.querySelector('#name') as HTMLElement;
        if (nameElement) nameElement.innerText = user_name;

        const numberElement = document.querySelector('#number') as HTMLElement;
        if (numberElement) numberElement.innerText = certificate_id;

        const cityElement = document.querySelector('#city') as HTMLElement;
        if (cityElement) cityElement.innerText = city;

        const addressElement = document.querySelector('#address') as HTMLElement;
        if (addressElement) addressElement.innerText = address;

        const barcodeElement = document.querySelector('#barcode') as HTMLImageElement;
        if (barcodeElement) barcodeElement.src = barcodeBase64;

        const companyElement = document.querySelector('#company_name') as HTMLElement;
        if (companyElement) companyElement.innerText = company_name;

        const dateElement = document.querySelector('#date') as HTMLElement;
        if (dateElement) dateElement.innerText = date;

        const userPhoneElement = document.querySelector('#user_phone') as HTMLElement;
        if (userPhoneElement) userPhoneElement.innerText = user_phone;

        const companyPhoneElement = document.querySelector('#company_phone') as HTMLElement;
        if (companyPhoneElement) companyPhoneElement.innerText = company_phone;

        const descriptionElement = document.querySelector('#description') as HTMLElement;
        if (descriptionElement) descriptionElement.innerText = description;
      },
      {
        user_name,
        certificate_id,
        city,
        address,
        barcodeBase64: barcodeBase64 || '',
        company_name,
        date,
        company_phone,
        user_phone,
        description,
      },
    );
    const name = path.basename(shortenedUrl);
    console.log({ name });
    const outputPath = path.resolve(__dirname, `../../PDF/${name}`);
    await page.pdf({
      path: outputPath,
      printBackground: true, // Ensure background images are printed
      width: '595px', // Explicitly set width to A4 dimensions
      height: '842px', // Explicitly set height to A4 dimensions
      preferCSSPageSize: true, // Prefer CSS-defined sizes over default dimensions
    });
    await browser.close();
    return outputPath;
  } catch (error) {
    console.error('Error generating PDF with Puppeteer:', error);
    throw error;
  }
};
