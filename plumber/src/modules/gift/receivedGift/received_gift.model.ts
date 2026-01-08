import { DataTypes, Model, Optional } from 'sequelize';
import sequelize from '../../../config/db';
import Gift from '../gift.model';
import User from '../../user/user.model';

interface ReceivedGiftAttributes {
  id: number;
  user_id: number;
  gift_id: number;
  createdAt?: Date;
  updatedAt?: Date;
  status: string;
  gift?: Gift;
  received_gifts?: ReceivedGift[];
}

interface ReceivedGiftCreationAttributes extends Optional<ReceivedGiftAttributes, 'id'> {}

class ReceivedGift extends Model<ReceivedGiftAttributes, ReceivedGiftCreationAttributes> {
  public id!: number;
  public user_id!: number;
  public gift_id!: number;
  public createdAt!: Date;
  public updatedAt!: Date;
public status!: string;
  public gift?: Gift;
}

ReceivedGift.init(
  {
    id: {
      type: DataTypes.INTEGER,
      autoIncrement: true,
      primaryKey: true,
    },
    user_id: {
      type: DataTypes.INTEGER,
      allowNull: false,
      references: {
        model: 'users',
        key: 'id',
      },
      onUpdate: 'CASCADE',
      onDelete: 'CASCADE',
    },
  status: {
      type: DataTypes.STRING,
      allowNull: false,
      defaultValue: 'Pending',
    },
    gift_id: {
      type: DataTypes.INTEGER,
      allowNull: false,
      references: {
        model: Gift,
        key: 'id',
      },
      onUpdate: 'CASCADE',
      onDelete: 'CASCADE',
    },
  },
  {
    sequelize,
    tableName: 'plumber_received_gifts',
    timestamps: true,
  },
);

ReceivedGift.belongsTo(User, { as: 'plumber', foreignKey: 'user_id' });
User.hasMany(ReceivedGift, {
  as: 'received_gifts',
  foreignKey: 'user_id',
});
ReceivedGift.belongsTo(Gift, { as: 'gift', foreignKey: 'gift_id' });

export default ReceivedGift;
