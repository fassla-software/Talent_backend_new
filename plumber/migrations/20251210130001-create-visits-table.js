'use strict';

module.exports = {
  async up(queryInterface, Sequelize) {
    await queryInterface.createTable('report_visits', {
      id: {
        type: Sequelize.INTEGER,
        primaryKey: true,
        autoIncrement: true,
        allowNull: false,
      },
      // Customer Information (معلومات العميل)
      customer_name: {
        type: Sequelize.STRING(255),
        allowNull: false,
      },
      company_name: {
        type: Sequelize.STRING(255),
        allowNull: true,
      },
      location: {
        type: Sequelize.STRING(500),
        allowNull: false,
      },
      region_province: {
        type: Sequelize.STRING(255),
        allowNull: true,
      },
      phone: {
        type: Sequelize.STRING(20),
        allowNull: false,
      },
      email: {
        type: Sequelize.STRING(255),
        allowNull: true,
      },
      client_type: {
        type: Sequelize.STRING(100),
        allowNull: true,
        comment: 'من report_dropdown_options',
      },
      visit_type: {
        type: Sequelize.STRING(100),
        allowNull: true,
        comment: 'من report_dropdown_options',
      },
      // Visit Details (تفاصيل الزيارة)
      visit_result: {
        type: Sequelize.STRING(100),
        allowNull: false,
        comment: 'من report_dropdown_options',
      },
      interest_level: {
        type: Sequelize.STRING(100),
        allowNull: true,
        comment: 'من report_dropdown_options',
      },
      purchase_readiness: {
        type: Sequelize.STRING(100),
        allowNull: true,
        comment: 'من report_dropdown_options',
      },
      authority_level: {
        type: Sequelize.STRING(100),
        allowNull: true,
        comment: 'من report_dropdown_options',
      },
      sales_value: {
        type: Sequelize.DECIMAL(15, 2),
        allowNull: true,
      },
      planned_purchase_date: {
        type: Sequelize.DATEONLY,
        allowNull: true,
      },
      outcome_classification: {
        type: Sequelize.STRING(100),
        allowNull: true,
        comment: 'من report_dropdown_options',
      },
      next_action: {
        type: Sequelize.STRING(100),
        allowNull: true,
        comment: 'من report_dropdown_options',
      },
      // Sales Classification (تصنيف المبيعات)
      sales_classification: {
        type: Sequelize.STRING(100),
        allowNull: true,
        comment: 'من report_dropdown_options (مباشر / غير مباشر)',
      },
      // Additional Notes
      additional_notes: {
        type: Sequelize.TEXT,
        allowNull: true,
      },
      // Photos and Documents
      photos: {
        type: Sequelize.JSON,
        allowNull: true,
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
  },

  async down(queryInterface) {
    await queryInterface.dropTable('report_visits');
  },
};

