import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';

class RegistrationBonusRule extends Model {
  public id!: number;
  public start_date!: Date;
  public end_date!: Date;
  public points!: number;
  public point_type!: 'fixed_points' | 'instant_withdrawal';

  public readonly createdAt!: Date;
  public readonly updatedAt!: Date;
}

RegistrationBonusRule.init(
  {
    id: {
      type: DataTypes.BIGINT.UNSIGNED,
      autoIncrement: true,
      primaryKey: true,
    },
    start_date: {
      type: DataTypes.DATE,
      allowNull: false,
    },
    end_date: {
      type: DataTypes.DATE,
      allowNull: false,
    },
    points: {
      type: DataTypes.INTEGER,
      allowNull: false,
    },
      point_type: {
      type: DataTypes.ENUM('fixed_points', 'instant_withdrawal'),
      allowNull: false,
      defaultValue: 'instant_withdrawal',
    },
  },
  {
    sequelize,
    tableName: 'registration_bonus',
    timestamps: true,
    underscored: true,
  }
);

export default RegistrationBonusRule;
