export const formatPhoneNumber = (phone: string | undefined): string => {
  if (phone) {
    // Ensure the phone number is always a string and add leading zero if necessary
    return phone.trim().startsWith('0') ? phone : '0' + phone;
  }
  return '';
};
