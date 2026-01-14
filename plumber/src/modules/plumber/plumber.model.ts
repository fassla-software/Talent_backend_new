import { DataTypes, Model, Optional } from 'sequelize';
import sequelize from '../../config/db';
import User from '../user/user.model';

export enum PlumberStatus {
  PENDING = 'PENDING',
  APPROVED = 'APPROVED',
  REJECTED = 'REJECTED',
}

export enum PlumberAccountStatus {
  ACTIVE = 'ACTIVE',
  INACTIVE = 'INACTIVE',
  DORMANT = 'DORMANT',
  PENDING = 'PENDING',
}

interface PlumberAttributes {
  id: number;
  user_id: number;
  city: string;
  area: string;
  nationality_id?: string;
  nationality_image1?: string;
  nationality_image2?: string;
  image?: string;
  is_verified: boolean;
  otp: string | null;
  expiration_date: Date | null;
  instant_withdrawal: number;
  withdraw_money: number;
  gift_points: number;
  fixed_points: number;
  inspector_id?: number | null;
  status: PlumberAccountStatus;
  createdAt: Date;
  updatedAt: Date;
  user?: User;
}

interface PlumberCreationAttributes
  extends Optional<
    PlumberAttributes,
    | 'id'
    | 'createdAt'
    | 'updatedAt'
    | 'is_verified'
    | 'expiration_date'
    | 'otp'
    | 'fixed_points'
    | 'gift_points'
    | 'instant_withdrawal'
    | 'image'
    | 'withdraw_money'
    | 'inspector_id'
    | 'status'
  > { }

class Plumber extends Model<PlumberAttributes, PlumberCreationAttributes> {
  public id!: number;
  public user_id!: number;
  public city!: string;
  public area!: string;
  public nationality_id?: string;
  public nationality_image1?: string;
  public nationality_image2?: string;
  public image?: string;
  public is_verified!: boolean;
  public otp?: string | null;
  public expiration_date?: Date | null;
  public createdAt!: Date;
  public updatedAt!: Date;
  public instant_withdrawal?: number;
  public withdraw_money?: number;
  public gift_points?: number;
  public fixed_points?: number;
  public inspector_id?: number | null;
  public status!: PlumberAccountStatus;

  // Define the association explicitly
  public user?: User;
}

// Initialize the model
Plumber.init(
  {
    id: {
      type: DataTypes.INTEGER,
      primaryKey: true,
      autoIncrement: true,
    },
    user_id: {
      type: DataTypes.INTEGER,
      allowNull: false,
      references: {
        model: 'users', // Ensure User is a Sequelize Model here
        key: 'id',
      },
      onDelete: 'CASCADE',
    },
    city: {
      type: DataTypes.STRING,
      allowNull: false,
    },
    area: {
      type: DataTypes.STRING,
      allowNull: false,
    },
    nationality_id: {
      type: DataTypes.STRING,
      allowNull: true,
    },
    nationality_image1: {
      type: DataTypes.STRING,
      allowNull: true,
    },
    nationality_image2: {
      type: DataTypes.STRING,
      allowNull: true,
    },
    image: {
      type: DataTypes.STRING,
      allowNull: true,
    },
    is_verified: {
      type: DataTypes.BOOLEAN,
      defaultValue: false,
      allowNull: false,
    },
    otp: {
      type: DataTypes.STRING,
      allowNull: true,
    },
    expiration_date: {
      type: DataTypes.DATE,
      allowNull: true,
    },
    instant_withdrawal: {
      type: DataTypes.INTEGER,
      allowNull: false,
      defaultValue: 0,
    },
    withdraw_money: {
      type: DataTypes.INTEGER,
      allowNull: false,
      defaultValue: 0,
    },
    gift_points: {
      type: DataTypes.INTEGER,
      allowNull: false,
      defaultValue: 0,
    },
    fixed_points: {
      type: DataTypes.INTEGER,
      allowNull: false,
      defaultValue: 0,
    },
    inspector_id: {
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: true,
      references: {
        model: 'users',
        key: 'id',
      },
    },
    status: {
      type: DataTypes.ENUM('ACTIVE', 'INACTIVE', 'DORMANT', 'PENDING'),
      allowNull: false,
      defaultValue: 'PENDING',
    },
    createdAt: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: DataTypes.NOW,
    },
    updatedAt: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: DataTypes.NOW,
    },
  },
  {
    sequelize,
    tableName: 'plumbers',
    timestamps: true,
    underscored: true,
  },
);

// Establish a relationship with the User model
Plumber.belongsTo(User, { foreignKey: 'user_id', as: 'user' });
User.hasMany(Plumber, { foreignKey: 'user_id', as: 'plumbers' });

export default Plumber;
