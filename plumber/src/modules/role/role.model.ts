import { DataTypes, Model, Optional } from 'sequelize';
import sequelize from '../../config/db';

export enum Roles {
  ADMIN   = 'admin',
  PLUMBER = 'plumber',
  Envoy   = 'envoy',
  TRADER  = 'trader',
}

// Define the Role attributes
interface RoleAttributes {
  id: number;
  name: string;
  guard_name: string;
  is_shop?: number;
  created_at?: Date | null;
  updated_at?: Date | null;
}

// Define the Role creation attributes (omit id as it's auto-incremented)
interface RoleCreationAttributes extends Optional<RoleAttributes, 'id'> {}

class Role extends Model<RoleAttributes, RoleCreationAttributes> implements RoleAttributes {
  public id!: number;
  public name!: string;
  public guard_name!: string;
  public is_shop?: number;
  public created_at?: Date | null;
  public updated_at?: Date | null;

  // timestamps are handled by Sequelize automatically
  public readonly createdAt!: Date;
  public readonly updatedAt!: Date;
}

// Define the model using TypeScript type support
Role.init(
  {
    id: {
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: false,
      primaryKey: true,
      autoIncrement: true,
    },
    name: {
      type: DataTypes.STRING(255),
      allowNull: false,
      unique: true,
    },
    guard_name: {
      type: DataTypes.STRING(255),
      allowNull: false,
    },
    is_shop: {
      type: DataTypes.TINYINT,
      allowNull: true,
      defaultValue: 0, // Default value is 0
    },
  },
  {
    sequelize,
    tableName: 'roles',
    modelName: 'Role', // Sequelize's model name
    timestamps: true, // Enable automatic handling of timestamps
    createdAt: 'created_at', // Custom column name for created timestamp
    updatedAt: 'updated_at', // Custom column name for updated timestamp
  },
);

export default Role;
