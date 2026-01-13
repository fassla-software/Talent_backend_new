import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';
import User from '../user/user.model';
import VisitReport from './visit-report.model';
import Trader from '../trader/trader.model';
import Plumber from '../plumber/plumber.model';

export enum VisitStatus {
  PENDING = 'PENDING',
  APPROVED = 'APPROVED',
  REJECTED = 'REJECTED',
}

class InspectionVisit extends Model {
  public id!: number;
  public inspector_id!: number;
  public trader_id?: number | null;
  public plumber_id?: number | null;
  public report_id?: number | null;
  public status!: VisitStatus;
  public check_in_at?: Date | null;
  public check_in_latitude?: number | null;
  public check_in_longitude?: number | null;
  public check_out_at?: Date | null;
  public check_out_latitude?: number | null;
  public check_out_longitude?: number | null;
  public createdAt!: Date;
  public updatedAt!: Date;

  // Relations
  public inspector?: User;
  public trader?: Trader;
  public plumber?: Plumber;
  public visitReport?: VisitReport;

  // Helper methods
  public isCheckedIn(): boolean {
    return this.check_in_at !== null && this.check_in_at !== undefined;
  }

  public isCheckedOut(): boolean {
    return this.check_out_at !== null && this.check_out_at !== undefined;
  }

  public hasVisitReport(): boolean {
    return this.report_id !== null && this.report_id !== undefined;
  }
}

InspectionVisit.init(
  {
    id: {
      type: DataTypes.INTEGER,
      primaryKey: true,
      autoIncrement: true,
    },
    inspector_id: {
      type: DataTypes.INTEGER,
      allowNull: false,
      references: {
        model: 'users',
        key: 'id',
      },
    },
    trader_id: {
      type: DataTypes.INTEGER,
      allowNull: true,
      references: {
        model: 'traders',
        key: 'id',
      },
      onDelete: 'SET NULL',
      onUpdate: 'CASCADE',
    },
    plumber_id: {
      type: DataTypes.INTEGER.UNSIGNED,
      allowNull: true,
      references: {
        model: 'plumbers',
        key: 'id',
      },
      onDelete: 'SET NULL',
      onUpdate: 'CASCADE',
    },
    report_id: {
      type: DataTypes.INTEGER,
      allowNull: true,
      references: {
        model: 'report_visits',
        key: 'id',
      },
    },
    status: {
      type: DataTypes.ENUM('PENDING', 'APPROVED', 'REJECTED'),
      allowNull: false,
      defaultValue: VisitStatus.PENDING,
    },
    check_in_at: {
      type: DataTypes.DATE,
      allowNull: true,
    },
    check_in_latitude: {
      type: DataTypes.DECIMAL(10, 8),
      allowNull: true,
    },
    check_in_longitude: {
      type: DataTypes.DECIMAL(11, 8),
      allowNull: true,
    },
    check_out_at: {
      type: DataTypes.DATE,
      allowNull: true,
    },
    check_out_latitude: {
      type: DataTypes.DECIMAL(10, 8),
      allowNull: true,
    },
    check_out_longitude: {
      type: DataTypes.DECIMAL(11, 8),
      allowNull: true,
    },
  },
  {
    sequelize,
    tableName: 'inspection_visits',
    timestamps: true,
    underscored: true,
  },
);

// Associations
InspectionVisit.belongsTo(User, {
  foreignKey: 'inspector_id',
  as: 'inspector',
});

InspectionVisit.belongsTo(Trader, {
  foreignKey: 'trader_id',
  as: 'trader',
});

Trader.hasMany(InspectionVisit, {
  foreignKey: 'trader_id',
  as: 'inspectionVisits',
});

InspectionVisit.belongsTo(Plumber, {
  foreignKey: 'plumber_id',
  as: 'plumber',
});

Plumber.hasMany(InspectionVisit, {
  foreignKey: 'plumber_id',
  as: 'inspectionVisits',
});

InspectionVisit.belongsTo(VisitReport, {
  foreignKey: 'report_id',
  as: 'visitReport',
});

VisitReport.hasOne(InspectionVisit, {
  foreignKey: 'report_id',
  as: 'inspectionVisit',
});

export default InspectionVisit;

