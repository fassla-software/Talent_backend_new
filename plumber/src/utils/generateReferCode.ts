import User from '../modules/user/user.model.js'
 export const generateUniqueReferralCode = async (): Promise<string> => {
  const generateCode = () => {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = '';
    for (let i = 0; i < 5; i++) {
      code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return code;
  };

  let unique = false;
  let code = '';
  while (!unique) {
    code = generateCode();
    const existingUser = await User.findOne({ where: { refer_code: code } });
    if (!existingUser) unique = true;
  }
  return code;
};
