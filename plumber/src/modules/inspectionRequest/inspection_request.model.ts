import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';
import InspectionRequestItem from './inspection_request-items.model';
import User from '../user/user.model';

// Define enum for request statuses
export enum RequestStatus {
  PENDING = 'PENDING',
  SEND = 'SEND',
  ASSIGNED = 'ASSIGNED',
  ACCEPTED = 'ACCEPTED',
  REJECTED = 'REJECTED',
  APPROVED = 'APPROVED', // for dashboard acceptance
  CANCELLED = 'CANCELLED', //  for dashboard rejection
}

class InspectionRequest extends Model {
  public id!: number;
  public requestor_id!: string;
  public user_name!: string;
  public user_phone!: string;
  public nationality_id?: string;
  public area!: string;
  public city!: string;
  public inspection_images!: string[] | null;
  public address!: string;
  public seller_name!: string;
  public seller_phone!: string;
  public inspector_id!: string;
  public certificate_id!: string;
  public user_lat!: number;
  public user_long!: number;
  public inspection_lat?: number;
  public inspection_long?: number;
  public inspection_date!: Date;
  public description?: string | null;
  public comment?: string | null;
  public images?: string[] | null;
  public status?: RequestStatus;
  public createdAt!: Date;
  public updatedAt!: Date;

  requestor?: User;
  inspector?: User;
  items?: InspectionRequestItem[];
}

InspectionRequest.init(
  {
    id: {
      type: DataTypes.INTEGER,
      primaryKey: true,
      autoIncrement: true,
    },
    requestor_id: {
      type: DataTypes.INTEGER,
      allowNull: true,
      references: {
        model: 'users',
        key: 'id',
      },
    },
    user_name: {
      type: DataTypes.STRING,
      allowNull: false,
    },
    user_phone: {
      type: DataTypes.STRING,
      allowNull: false,
    },
    nationality_id: {
      type: DataTypes.STRING,
      allowNull: true,
    },
    area: {
      type: DataTypes.STRING,
      allowNull: false,
    },
    city: {
      type: DataTypes.STRING,
      allowNull: false,
    },
    address: {
      type: DataTypes.STRING,
      allowNull: false,
    },
    seller_name: {
      type: DataTypes.STRING,
      allowNull: true,
    },
    seller_phone: {
      type: DataTypes.STRING,
      allowNull: true,
    },
    certificate_id: {
      type: DataTypes.STRING,
      allowNull: true,
    },
    inspection_date: {
      type: DataTypes.DATE,
      allowNull: true,
    },
    inspector_id: {
      type: DataTypes.INTEGER,
      allowNull: true,
      references: {
        model: 'users',
        key: 'id',
      },
    },
    inspection_images: {
      type: DataTypes.JSON,
      allowNull: true,
      get() {
        const rawValue = this.getDataValue('inspection_images');
        return rawValue ? JSON.parse(rawValue) : null;
      },
      set(value) {
        this.setDataValue('inspection_images', JSON.stringify(value));
      },
    },
    inspection_lat: {
      type: DataTypes.DECIMAL(10, 8),
      allowNull: true,
    },
    inspection_long: {
      type: DataTypes.DECIMAL(11, 8),
      allowNull: true,
    },
    description: {
      type: DataTypes.STRING,
      allowNull: true,
    },
    comment: {
      type: DataTypes.STRING,
      allowNull: true,
    },
    images: {
      type: DataTypes.JSON,
      allowNull: true,
      get() {
        const rawValue = this.getDataValue('images');
        return rawValue ? JSON.parse(rawValue) : null;
      },
      set(value) {
        this.setDataValue('images', JSON.stringify(value));
      },
    },
    user_lat: {
      type: DataTypes.DECIMAL(10, 8),
      allowNull: true,
    },
    user_long: {
      type: DataTypes.DECIMAL(11, 8),
      allowNull: true,
    },
    status: {
      type: DataTypes.STRING,
      allowNull: false,
      defaultValue: RequestStatus.PENDING,
      validate: {
        isIn: [
          [
            RequestStatus.PENDING,
            RequestStatus.SEND,
            RequestStatus.ASSIGNED,
            RequestStatus.ACCEPTED,
            RequestStatus.REJECTED,
            RequestStatus.APPROVED,
            RequestStatus.CANCELLED,
          ],
        ],
      },
    },
  },
  {
    sequelize,
    tableName: 'inspection_requests',
    timestamps: true,
    underscored: true,
  },
);

// Associations
InspectionRequest.hasMany(InspectionRequestItem, {
  foreignKey: 'inspection_request_id', // FK in InspectionRequestItem
  as: 'items', // Alias for eager loading
});

InspectionRequestItem.belongsTo(InspectionRequest, {
  foreignKey: 'inspection_request_id',
  as: 'request',
});

InspectionRequest.belongsTo(User, {
  foreignKey: 'requestor_id',
  as: 'requestor',
});

User.hasMany(InspectionRequest, {
  foreignKey: 'requestor_id',
  as: 'requests',
});

InspectionRequest.belongsTo(User, {
  foreignKey: 'inspector_id',
  as: 'inspector',
});

User.hasMany(InspectionRequest, {
  foreignKey: 'inspector_id',
  as: 'inspect_requests',
});
export default InspectionRequest;
