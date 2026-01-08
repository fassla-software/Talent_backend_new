'use strict';

module.exports = {
  up: async (queryInterface, Sequelize) => {
    const now = new Date();
    
    await queryInterface.bulkInsert('report_dropdown_options', [
      // Client Type
      { dropdown_type: 'client_type', key: 'direct_client', value_en: 'Direct Client', value_ar: 'عميل مباشر', created_at: now, updated_at: now },
      { dropdown_type: 'client_type', key: 'trader', value_en: 'Trader', value_ar: 'تاجر', created_at: now, updated_at: now },
      { dropdown_type: 'client_type', key: 'distributer', value_en: 'Distributer', value_ar: 'موزع', created_at: now, updated_at: now },
      { dropdown_type: 'client_type', key: 'retailer', value_en: 'Retailer', value_ar: 'بائع تجزئة', created_at: now, updated_at: now },

      // Visit Type
      { dropdown_type: 'visit_type', key: 'new_visit', value_en: 'New Visit', value_ar: 'زيارة جديدة', created_at: now, updated_at: now },
      { dropdown_type: 'visit_type', key: 'follow_up', value_en: 'Follow-up', value_ar: 'متابعة', created_at: now, updated_at: now },
      { dropdown_type: 'visit_type', key: 'inspection', value_en: 'Inspection', value_ar: 'فحص', created_at: now, updated_at: now },
      { dropdown_type: 'visit_type', key: 'support', value_en: 'Support', value_ar: 'دعم', created_at: now, updated_at: now },
      { dropdown_type: 'visit_type', key: 'delivery', value_en: 'Delivery', value_ar: 'توصيل', created_at: now, updated_at: now },

      // Visit Result
      { dropdown_type: 'visit_result', key: 'deal_closed', value_en: 'Deal Closed', value_ar: 'تم إغلاق الصفقة', created_at: now, updated_at: now },
      { dropdown_type: 'visit_result', key: 'demo_scheduled', value_en: 'Demo Scheduled', value_ar: 'تم جدولة عرض توضيحي', created_at: now, updated_at: now },
      { dropdown_type: 'visit_result', key: 'quote_requested', value_en: 'Quote Requested', value_ar: 'طلب عرض سعر', created_at: now, updated_at: now },
      { dropdown_type: 'visit_result', key: 'requires_follow_up', value_en: 'Requires Follow-up', value_ar: 'يحتاج متابعة', created_at: now, updated_at: now },
      { dropdown_type: 'visit_result', key: 'not_interested', value_en: 'Not Interested', value_ar: 'غير مهتم', created_at: now, updated_at: now },

      // Interest Level
      { dropdown_type: 'interest_level', key: 'high_interest', value_en: 'High Interest', value_ar: 'اهتمام عالي', created_at: now, updated_at: now },
      { dropdown_type: 'interest_level', key: 'medium_interest', value_en: 'Medium Interest', value_ar: 'اهتمام متوسط', created_at: now, updated_at: now },
      { dropdown_type: 'interest_level', key: 'low_interest', value_en: 'Low Interest', value_ar: 'اهتمام منخفض', created_at: now, updated_at: now },

      // Purchase Readiness
      { dropdown_type: 'purchase_readiness', key: 'immediate', value_en: 'Immediate', value_ar: 'فوري', created_at: now, updated_at: now },
      { dropdown_type: 'purchase_readiness', key: 'short_term', value_en: 'Short-term (2-4 months)', value_ar: 'قصير المدى (2-4 أشهر)', created_at: now, updated_at: now },
      { dropdown_type: 'purchase_readiness', key: 'long_term', value_en: 'Long-term (4+ months)', value_ar: 'طويل المدى 4+ أشهر', created_at: now, updated_at: now },
      { dropdown_type: 'purchase_readiness', key: 'unspecified', value_en: 'Unspecified', value_ar: 'غير محدد', created_at: now, updated_at: now },

      // Outcome Classification
      { dropdown_type: 'outcome_classification', key: 'high', value_en: 'High', value_ar: 'عالي', created_at: now, updated_at: now },
      { dropdown_type: 'outcome_classification', key: 'medium', value_en: 'Medium', value_ar: 'متوسط', created_at: now, updated_at: now },
      { dropdown_type: 'outcome_classification', key: 'weak', value_en: 'Weak', value_ar: 'ضعيف', created_at: now, updated_at: now },
      { dropdown_type: 'outcome_classification', key: 'deal_closed', value_en: 'Deal Closed', value_ar: 'تم إغلاق الصفقة', created_at: now, updated_at: now },
      { dropdown_type: 'outcome_classification', key: 'deal_lost', value_en: 'Deal Lost', value_ar: 'خسارة الصفقة', created_at: now, updated_at: now },
      { dropdown_type: 'outcome_classification', key: 'needs_nurturing', value_en: 'Needs Nurturing', value_ar: 'يحتاج رعاية', created_at: now, updated_at: now },

      // Authority Level
      { dropdown_type: 'authority_level', key: 'decision_maker', value_en: 'Decision Maker', value_ar: 'صانع قرار', created_at: now, updated_at: now },
      { dropdown_type: 'authority_level', key: 'influencer', value_en: 'Influencer', value_ar: 'مؤثر', created_at: now, updated_at: now },
      { dropdown_type: 'authority_level', key: 'end_user', value_en: 'End User', value_ar: 'مستخدم نهائي', created_at: now, updated_at: now },
      { dropdown_type: 'authority_level', key: 'gatekeeper', value_en: 'Gatekeeper', value_ar: 'حارس البوابة', created_at: now, updated_at: now },

      // Next Action
      { dropdown_type: 'next_action', key: 'follow_up_call', value_en: 'Follow-up Call', value_ar: 'مكالمة متابعة', created_at: now, updated_at: now },
      { dropdown_type: 'next_action', key: 'send_quote', value_en: 'Send Quote', value_ar: 'إرسال عرض سعر', created_at: now, updated_at: now },
      { dropdown_type: 'next_action', key: 'schedule_demo', value_en: 'Schedule Demo', value_ar: 'جدولة عرض توضيحي', created_at: now, updated_at: now },
      { dropdown_type: 'next_action', key: 'field_visit', value_en: 'Field Visit', value_ar: 'زيارة ميدانية', created_at: now, updated_at: now },
      { dropdown_type: 'next_action', key: 'no_action_required', value_en: 'No Action Required', value_ar: 'لا يوجد إجراء مطلوب', created_at: now, updated_at: now },

      // Sales Classification (Static)
      { dropdown_type: 'sales_classification', key: 'direct_sales', value_en: 'Direct Sales', value_ar: 'مبيعات مباشرة', is_static: true, created_at: now, updated_at: now },
      { dropdown_type: 'sales_classification', key: 'indirect_sales', value_en: 'Indirect Sales', value_ar: 'مبيعات غير مباشرة', is_static: true, created_at: now, updated_at: now },
    ]);
  },

  down: async (queryInterface, Sequelize) => {
    await queryInterface.bulkDelete('report_dropdown_options', null, {});
  }
};