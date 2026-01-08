import User from './user.model';

export const getUsers = async () => {
  const users = await User.findAll();
  return users;
};
