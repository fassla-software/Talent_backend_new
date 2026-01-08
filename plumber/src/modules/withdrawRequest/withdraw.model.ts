import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';
import User from '../user/user.model';

export interface WithdrawRequestRow {
  id: string;
  status: string;
  amount: string;
  payment_identifier: string;
  transaction_type: string;
}

export enum WithdrawRequestStatus {
  PENDING = 'pending',
  APPROVED = 'approved',
  REJECTED = 'rejected',
}

export enum TransactionType {
  WALLET = 'wallet',
  BANK = 'bank',
  MEEZA = 'meeza',
}

class WithdrawRequest extends Model {
  public id!: number; // Primary Key
  public requestor_id!: number; // Foreign Key referencing User
  public amount!: number;
  public status!: 'pending' | 'approved' | 'rejected';
  public payment_identifier!: string;
  public image?: string;
  public transaction_type!: 'wallet' | 'bank' | 'meeza';
  public request_date!: Date;
  public processed_date!: Date | null;

  // Timestamps
  public readonly created_at!: Date;
  public readonly updated_at!: Date;

  requestor?: User;
}

// Initialize the model
WithdrawRequest.init(
  {
    id: {
      type: DataTypes.INTEGER,
      primaryKey: true,
      autoIncrement: true,
      allowNull: false,
    },
    requestor_id: {
      type: DataTypes.INTEGER,
      allowNull: false,
      references: {
        model: 'users',
        key: 'id',
      },
    },
    amount: {
      type: DataTypes.DECIMAL(10, 2),
      allowNull: false,
    },
    status: {
      type: DataTypes.ENUM,
      values: Object.values(WithdrawRequestStatus),
      allowNull: false,
      defaultValue: 'pending',
    },
    payment_identifier: {
      type: DataTypes.STRING,
      allowNull: false,
    },
    transaction_type: {
      type: DataTypes.ENUM,
      values: Object.values(TransactionType),
      allowNull: false,
    },
    image: {
      type: DataTypes.STRING,
      allowNull: true,
    },
    request_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: DataTypes.NOW,
    },
    processed_date: {
      type: DataTypes.DATE,
      allowNull: true,
    },
  },
  {
    sequelize,
    underscored: true,
    tableName: 'plumber_withdraw_requests',
    timestamps: true,
    paranoid: true,
  },
);

export default WithdrawRequest;

WithdrawRequest.belongsTo(User, {
  foreignKey: 'requestor_id',
  as: 'requestor',
  onUpdate: 'CASCADE',
  onDelete: 'CASCADE',
});
