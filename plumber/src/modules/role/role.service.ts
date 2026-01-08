import HttpError from '../../utils/HttpError';
import ModelHasRoles from './model_has_roles.model';
import Roles from './role.model';
import User from '../user/user.model';

export const getRole = async (user_id: number) => {
  const model = await ModelHasRoles.findOne({ where: { model_id: user_id } });
  if (!model) {
    throw new HttpError('user not assigned', 401);
  }
  const role = await Roles.findByPk(model.role_id);
  if (!role) {
    throw new HttpError('Role not found', 404);
  }
  return role.name;
};

export const assignRole = async (user_id: number, roleName: string) => {
  const role = await Roles.findOne({ where: { name: roleName } });
  if (!role) {
    throw new HttpError('Role not found', 404);
  }
  const model = await ModelHasRoles.create({
    model_id: user_id,
    role_id: role.id,
    model_type: 'App\\Models\\User',
  });
  if (!model) {
    throw new HttpError('user not assigned', 401);
  }

  return role.name;
};

export const getUsersByRole = async (roleName: string) => {
  const role = await Roles.findOne({ where: { name: roleName } });
  if (!role) {
    throw new HttpError('Role not found', 404);
  }
  const modelHasRoles = await ModelHasRoles.findAll({
    where: { role_id: role.id, model_type: 'App\\Models\\User' },
  });
  if (!modelHasRoles || modelHasRoles.length === 0) {
    return [];
  }
  const userIds = modelHasRoles.map(mhr => mhr.model_id);
  const users = await User.findAll({
    where: { id: userIds },
    attributes: ['id', 'name', 'phone', 'is_active'],
  });
  return users;
};

