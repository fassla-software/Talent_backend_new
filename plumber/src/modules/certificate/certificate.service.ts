import { generateCardWithBackground } from '../../utils/certificateGenerator';
import { shortenUrl, viewUrl } from '../../utils/urlUtiles';
import User from '../user/user.model';
import Certificate from './certificate.model';

type certificateType = {
  certificate_id: string;
  user_name: string;
  city: string;
  address: string;
  company_name: string;
  date: string;
  company_phone: string;
  user_phone: string;
  url: string;
};

export const createCertificatePDF = async (certificateData: certificateType) => {
  try {
    const { url } = certificateData;
    const shortenedUrl = shortenUrl(url);
    console.log({ 'Shortened URL:': shortenedUrl });

    // Generate the PDF with the generated token as the barcode
    const pdfPath = await generateCardWithBackground(certificateData, shortenedUrl);
    return pdfPath;
  } catch (error) {
    console.error('Error creating certificate PDF:', error);
    throw error;
  }
};

export const getCertificates = async (filter: {
  phone?: string;
  nationality_id?: string;
  limit?: number;
  skip?: number;
}) => {
  const { phone, nationality_id, limit = 10, skip = 0 } = filter;
  const certificates = await Certificate.findAll({
    where: {
      ...(phone && { user_phone: phone }), // Only include if phone is provided
      ...(nationality_id && { nationality_id }), // Only include if nationality_id is provided
    },
    include: {
      model: User,
      as: 'plumber',
      attributes: ['name'],
    },
    limit: Number(limit),
    offset: Number(skip),
    order: [['createdAt', 'DESC']],
  });

  return certificates.map(cer => {
    return {
      ...cer.toJSON(),
      url: viewUrl(cer.file_name),
    };
  });
};
