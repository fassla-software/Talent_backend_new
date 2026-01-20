import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';
import Media from '../media/media.model';
import InspectionRequest from '../inspectionRequest/inspection_request.model';
import ReceivedGift from '../gift/receivedGift/received_gift.model';
import { PlumberStatus } from '../plumber/plumber.model';
import type EnvoySetting from '../envoy/envoy.model';

class User extends Model {
  public id!: number;
  public name!: string;
  public phone!: string;
  public email!: string | null;
  public mediaId!: number | null; // Foreign key for Media
  public password!: string;
  public country!: string | null;
  public status!: PlumberStatus;
  public gender!: string | null;
  public date_of_birth!: Date | null;
  public is_active!: boolean;
  public email_verified_at!: Date | null;
  public phone_verified_at!: Date | null;
  public remember_token!: string;
  public last_login_token!: string | null; // New column
  public createdAt!: Date;
  public updatedAt!: Date;
  public refer_code!: string | null;
  public refer_code_used!: number;
  public otp!: string | null;
  public expiration_date!: Date | null;
  public device_token!: string | null;


  // to view data
  requests?: InspectionRequest[];
  received_gifts?: ReceivedGift[];
  approved_requests_count?: number;
  canceled_requests_count?: number;
  envoySetting?: EnvoySetting;
}

User.init(
  {
    id: {
      type: DataTypes.BIGINT.UNSIGNED,
      primaryKey: true,
      autoIncrement: true,
    },
    name: {
      type: DataTypes.STRING,
      allowNull: false,
    },
    phone: {
      type: DataTypes.STRING,
      allowNull: false,
      unique: true,
    },
    email: {
      type: DataTypes.STRING,
      allowNull: true,
    },
    mediaId: {
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: true,
      references: {
        model: Media,
        key: 'id',
      },
      onDelete: 'SET NULL',
    },
    password: {
      type: DataTypes.STRING,
      allowNull: false,
    },
    gender: {
      type: DataTypes.STRING,
      allowNull: true,
    },
    country: {
      type: DataTypes.STRING,
      allowNull: true,
    },
    date_of_birth: {
      type: DataTypes.DATEONLY,
      allowNull: true,
    },
    is_active: {
      type: DataTypes.BOOLEAN,
      defaultValue: true,
    },
    email_verified_at: {
      type: DataTypes.DATE,
      allowNull: true,
    },
    phone_verified_at: {
      type: DataTypes.DATE,
      allowNull: true,
    },
    remember_token: {
      type: DataTypes.STRING,
      allowNull: true,
    },
    last_login_token: {
      type: DataTypes.STRING,
      allowNull: true, // Token is optional
    },
    refer_code: {
      type: DataTypes.STRING(5),
      unique: true,
      allowNull: true,
    },
    refer_code_used: {
      type: DataTypes.INTEGER.UNSIGNED,
      allowNull: false,
      defaultValue: 0,
    },
    otp: {
      type: DataTypes.STRING,
      allowNull: true,
    },
    expiration_date: {
      type: DataTypes.DATE,
      allowNull: true,
    },
    device_token: {
      type: DataTypes.STRING,
      allowNull: true,
    },

    status: { type: DataTypes.STRING },
  },
  {
    sequelize,
    tableName: 'users',
    timestamps: true,
    underscored: true, // Laravel uses snake_case for columns, set to true
    paranoid: true, // Enables soft deletes (optional)
  },
);

export default User;
