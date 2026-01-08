import { DataTypes, Model, Optional } from 'sequelize';
import sequelize from '../../config/db';

// Define the attributes for ModelHasRoles
interface ModelHasRolesAttributes {
  role_id: number;
  model_type: string;
  model_id: number;
}

interface ModelHasRolesCreationAttributes extends Optional<ModelHasRolesAttributes, 'role_id'> {}

class ModelHasRoles
  extends Model<ModelHasRolesAttributes, ModelHasRolesCreationAttributes>
  implements ModelHasRolesAttributes
{
  public role_id!: number;
  public model_type!: string;
  public model_id!: number;
}

ModelHasRoles.init(
  {
    role_id: {
      type: DataTypes.BIGINT.UNSIGNED,
      primaryKey: true,
      allowNull: false,
      references: {
        model: 'roles',
        key: 'id',
      },
    },
    model_type: {
      type: DataTypes.STRING(255),
      primaryKey: true,
      allowNull: false,
    },
    model_id: {
      type: DataTypes.BIGINT.UNSIGNED,
      primaryKey: true,
      allowNull: false,
      references: {
        model: 'users',
        key: 'id',
      },
    },
  },
  {
    sequelize,
    tableName: 'model_has_roles',
    timestamps: false,
  },
);

export default ModelHasRoles;
