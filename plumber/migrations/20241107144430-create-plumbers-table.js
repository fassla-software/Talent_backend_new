'use strict';

module.exports = {
  async up(queryInterface, Sequelize) {
    const tables = await queryInterface.sequelize.getQueryInterface().showAllTables();

    // Create 'plumbers' table if it does not exist
    if (!tables.includes('plumbers')) {
      await queryInterface.createTable('plumbers', {
        id: {
          type: Sequelize.INTEGER,
          allowNull: false,
          autoIncrement: true,
          primaryKey: true,
        },
        user_id: {
          type: Sequelize.INTEGER,
          allowNull: false,
        },
        city: {
          type: Sequelize.STRING,
          allowNull: false,
        },
        area: {
          type: Sequelize.STRING,
          allowNull: false,
        },
        nationality_id: {
          type: Sequelize.STRING,
          allowNull: true,
        },
        nationality_image1: {
          type: Sequelize.STRING,
          allowNull: true,
        },
        nationality_image2: {
          type: Sequelize.STRING,
          allowNull: true,
        },
        is_verified: {
          type: Sequelize.BOOLEAN,
          allowNull: false,
          defaultValue: false,
        },
        otp: {
          type: Sequelize.STRING,
          allowNull: true,
        },
        expiration_date: {
          type: Sequelize.DATE,
          allowNull: true,
        },
        instant_withdrawal: {
          type: Sequelize.INTEGER,
          allowNull: false,
          defaultValue: 0,
        },
        gift_points: {
          type: Sequelize.INTEGER,
          allowNull: false,
          defaultValue: 0,
        },
        fixed_points: {
          type: Sequelize.INTEGER,
          allowNull: false,
          defaultValue: 0,
        },
        created_at: {
          type: Sequelize.DATE,
          allowNull: false,
          defaultValue: Sequelize.NOW,
        },
        updated_at: {
          type: Sequelize.DATE,
          allowNull: false,
          defaultValue: Sequelize.NOW,
        },
      });
    }

    // Add foreign key constraint for user_id if it does not already exist
    const foreignKeys = await queryInterface.getForeignKeysForTables(['plumbers']);
    if (!foreignKeys['plumbers'].includes('fk_user_id_plumbers')) {
      await queryInterface.addConstraint('plumbers', {
        fields: ['user_id'],
        type: 'foreign key',
        name: 'fk_user_id_plumbers',
        references: {
          table: 'users',
          field: 'id',
        },
        onDelete: 'CASCADE',
        onUpdate: 'CASCADE',
      });
    }
  },

  async down(queryInterface) {
    const columns = await queryInterface.describeTable('plumbers');

    const removeColumns = [
      'nationality_id',
      'nationality_image1',
      'nationality_image2',
      'is_verified',
      'otp',
      'expiration_date',
      'instant_withdrawal',
      'gift_points',
      'fixed_points',
    ];

    for (const column of removeColumns) {
      if (columns[column]) {
        await queryInterface.removeColumn('plumbers', column);
      }
    }

    await queryInterface.removeConstraint('plumbers', 'fk_user_id_plumbers');
  },
};
