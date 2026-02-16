import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';



class PendingBonus extends Model {
  public id!: number;
  public new_user_id!: number;         // المستخدم الجديد اللي سجل
  public referrer_id!: number | null;  // صاحب كود الإحالة (لو فيه)
  public points!: number;
  public status!: 'PENDING' | 'COMPLETED';
  public readonly createdAt!: Date;
  public readonly updatedAt!: Date;
}

PendingBonus.init(
  {
    id: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    new_user_id: { type: DataTypes.INTEGER, allowNull: false },
    referrer_id: { type: DataTypes.INTEGER, allowNull: true }, // nullable
    points: { type: DataTypes.INTEGER, allowNull: false },
    status: {
      type: DataTypes.ENUM('PENDING', 'COMPLETED'),
      allowNull: false,
      defaultValue: 'PENDING',
    },
    point_type: {
      type: DataTypes.ENUM('fixed_points', 'instant_withdrawal'),
      allowNull: false,
      defaultValue: 'instant_withdrawal',
    },
  },
  {
    sequelize,
    tableName: 'pending_bonuses',
    timestamps: true,
  }
);
export { PendingBonus };