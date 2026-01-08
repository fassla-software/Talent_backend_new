import ReportDropdownOption from './dropdown-options.model';

class ReportDropdownService {
  async getDropdownOptions(type: string) {
    return await ReportDropdownOption.findAll({
      where: { dropdown_type: type },
    });
  }

  async getAllDropdownTypes() {
    const types = await ReportDropdownOption.findAll({
      attributes: ['dropdown_type'],
      group: ['dropdown_type'],
    });
    return types.map(item => item.dropdown_type);
  }

  async createOption(data: any) {
    const allowedTypes = [
      'client_type', 'visit_type', 'visit_result', 'interest_level',
      'purchase_readiness', 'outcome_classification', 'authority_level', 
      'next_action', 'sales_classification'
    ];
    
    if (!allowedTypes.includes(data.dropdown_type)) {
      throw new Error('Invalid dropdown type. Only predefined dropdown types are allowed.');
    }
    
    if (data.dropdown_type === 'sales_classification') {
      throw new Error('Cannot add options to sales_classification dropdown');
    }
    
    return await ReportDropdownOption.create(data);
  }

  async updateOption(id: number, data: any) {
    const option = await ReportDropdownOption.findByPk(id);
    if (!option) {
      throw new Error('Option not found');
    }
    
    if (option.is_static) {
      throw new Error('Cannot modify static dropdown options');
    }
    
    return await option.update(data);
  }

  async deleteOption(id: number) {
    const option = await ReportDropdownOption.findByPk(id);
    if (!option) {
      throw new Error('Option not found');
    }
    
    if (option.is_static) {
      throw new Error('Cannot delete static dropdown options');
    }
    
    return await option.destroy();
  }
}

export default new ReportDropdownService();