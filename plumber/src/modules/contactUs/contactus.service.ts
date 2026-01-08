import HttpError from '../../utils/HttpError';
import ContactUs from './contactus.model';
import { IContactUs } from './dto/contactus.dto';

export const createContact = async (data: IContactUs): Promise<ContactUs> => {
  console.log({ data });
  const contact = await ContactUs.create(data);
  return contact;
};

export const getContactById = async (id: bigint): Promise<ContactUs | null> => {
  const contact = await ContactUs.findByPk(id);
  if (!contact) {
    throw new HttpError('Contact entry not found', 404);
  }
  return contact;
};

export const updateContact = async (id: number, data: Partial<ContactUs>): Promise<ContactUs> => {
  const contact = await ContactUs.findByPk(id);
  if (!contact) {
    throw new HttpError('Contact entry not found', 404);
  }
  await contact.update(data);
  return contact;
};

export const deleteContact = async (id: bigint): Promise<void> => {
  const contact = await ContactUs.findByPk(id);
  if (!contact) {
    throw new HttpError('Contact entry not found', 404);
  }
  await contact.destroy();
};

export const listContacts = async (limit: number = 10, offset: number = 0): Promise<ContactUs[]> => {
  return await ContactUs.findAll({ limit, offset });
};
