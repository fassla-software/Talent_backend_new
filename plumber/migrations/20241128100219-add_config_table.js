'use strict';

module.exports = {
  up: async (queryInterface, Sequelize) => {
    await queryInterface.createTable('app_configs', {
      id: {
        type: Sequelize.INTEGER,
        primaryKey: true,
        autoIncrement: true,
        allowNull: false,
      },
      key: {
        type: Sequelize.STRING,
        allowNull: false,
        unique: true,
      },
      value: {
        type: Sequelize.TEXT,
        allowNull: false,
      },
      createdAt: {
        type: DataTypes.DATE,
        field: 'created_at', // Maps to the database column
      },
      updatedAt: {
        type: DataTypes.DATE,
        field: 'updated_at', // Maps to the database column
      },
    },
    {
      tableName: 'app_configs',
      timestamps: true, // Enables Sequelize to use createdAt/updatedAt
    }
    });
  },

  down: async queryInterface => {
    await queryInterface.dropTable('app_configs');
  },
};
