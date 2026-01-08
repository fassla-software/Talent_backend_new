import { DataTypes, Model } from 'sequelize';
import sequelize from '../../config/db';
import InspectionRequest from '../inspectionRequest/inspection_request.model';
import User from '../user/user.model';
import VisitReport from './visit-report.model';

export enum VisitStatus {
  PENDING = 'PENDING',
  APPROVED = 'APPROVED',
  REJECTED = 'REJECTED',
}

class InspectionVisit extends Model {
  public id!: number;
  public inspection_request_id!: number;
  public inspector_id!: number;
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
  public inspectionRequest?: InspectionRequest;
  public inspector?: User;
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
    inspection_request_id: {
      type: DataTypes.INTEGER,
      allowNull: false,
      references: {
        model: 'inspection_requests',
        key: 'id',
      },
    },
    inspector_id: {
      type: DataTypes.INTEGER,
      allowNull: false,
      references: {
        model: 'users',
        key: 'id',
      },
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
InspectionVisit.belongsTo(InspectionRequest, {
  foreignKey: 'inspection_request_id',
  as: 'inspectionRequest',
});

InspectionRequest.hasMany(InspectionVisit, {
  foreignKey: 'inspection_request_id',
  as: 'visits',
});

InspectionVisit.belongsTo(User, {
  foreignKey: 'inspector_id',
  as: 'inspector',
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

