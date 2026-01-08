import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';

class ReferralConfig extends Model {
  public id!: number;
  public referral_point!: number;
  public point_type!: 'fixed_points' | 'instant_withdrawal';
  public readonly createdAt!: Date;
  public readonly updatedAt!: Date;
}

ReferralConfig.init(
  {
    referral_point: {
      type: DataTypes.INTEGER,
      allowNull: false,
      defaultValue: 0,
    },
    point_type: {
      type: DataTypes.ENUM('fixed_points', 'instant_withdrawal'),
      allowNull: false,
      defaultValue: 'instant_withdrawal',
    },
  },
  {
    sequelize,
    tableName: 'referral_configs',
    timestamps: true, 

  }
);

export default ReferralConfig;

