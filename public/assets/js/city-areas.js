// City-Area Data Mapping
const cityAreasData = {
    'القاهرة': ['وسط البلد', 'المعادى', 'مدينة نصر', 'المقطم', 'التجمع الخامس', 'الشروق', 'حلوان', 'الزاوية الحمراء', 'دار السلام', '15مايو', 'العباسية', 'الزيتون', 'حدائق القبة', 'المرج', 'السلام', 'الغمراوى', 'الوايلى', 'الزهراء', 'الشرابية', 'الحلمية', 'عين شمس', 'حلمية الزيتون', 'الزمالك', 'السيدة زينب'],
    'الجيزة': ['الهرم', '6أكتوبر', 'بولاق الدكرور', 'الدقى', 'فيصل', 'الشيخ زايد', 'أكتوبر الجديدة', 'العمرانية', 'المنيب', 'الطالبية', 'المهندسين', 'كرداسة', 'أبو النمرس', 'الحوامدية', 'البدرشين', 'الصف', 'إمبابة', 'المريوطية', 'المعتمدية', 'أطفيح', 'العياط', 'منشأة القناطر'],
    'القليوبية': ['بنها', 'قليوب', 'شبرا الخيمة', 'طوخ', 'كفر شكر', 'الخانكة', 'شبين القناطر', 'العبور', 'الخصوص', 'قها'],
    'البحيرة': ['دمنهور', 'كفر الدوار', 'رشيد', 'إدكو', 'المحمودية', 'إيتاي البارود', 'كوم حمادة', 'أبو حمص', 'حوش عيسى'],
    'دمياط': ['دمياط القديمة', 'فارسكور', 'كفر سعد', 'الرزقة', 'رأس البر', 'عزبة البرج'],
    'الدقهلية': ['المنصورة', 'طلخا', 'ميت غمر', 'دكرنس', 'المنزلة', 'نبروه', 'الجمالية', 'الكردي', 'ميت سلسيل', 'بني عبيد', 'السنبلاوين'],
    'كفر الشيخ': ['كفر الشيخ', 'دسوق', 'فوه', 'قلين', 'سيدي سالم', 'الرياض', 'بلطيم', 'بيلا'],
    'الغربية': ['طنطا', 'المحلة الكبرى', 'زفتى', 'قطور', 'بسيون', 'السنطة'],
    'المنوفية': ['شبين الكوم', 'قويسنا', 'تلا', 'الشهداء', 'منوف', 'سرس الليان', 'الباجور', 'اشمون'],
    'الشرقية': ['الزقازيق', 'بلبيس', 'منيا القمح', 'ههيا', 'أبو حماد', 'الإبراهيمية', 'فاقوس', 'كفر صقر', 'أولاد صقر', 'الحسينية', 'الصالحية'],
    'بورسعيد': ['بورفؤاد', 'حي العرب', 'حي الضواحي', 'حي المناخ', 'حي الجنوب'],
    'الإسماعيلية': ['الإسماعيلية', 'القنطرة شرق', 'القنطرة غرب', 'فايد', 'أبو صوير'],
    'بني سويف': ['بني سويف', 'الواسطى', 'إهناسيا', 'ببا', 'سمسطا', 'الفشن'],
    'الفيوم': ['الفيوم', 'سنورس', 'إطسا', 'طامية', 'أبشواي'],
    'المنيا': ['المنيا', 'المنيا الجديدة', 'مطاي', 'أبو قرقاص', 'ملوي'],
    'أسيوط': ['أسيوط', 'ديروط', 'الغنايم', 'أبو تيج', 'القوصية', 'منفلوط'],
    'سوهاج': ['سوهاج', 'جرجا', 'طهطا', 'البلينا', 'المراغة', 'دار السلام', 'أخميم', 'المنشأة', 'جهينة الغربية'],
    'قنا': ['قنا', 'نجع حمادي', 'دشنا', 'قفط', 'أبو تشت']
};

/**
 * Initialize cascading city-area dropdowns
 * @param {string} citySelectId - ID of the city select element
 * @param {string} areaSelectId - ID of the area select element
 * @param {string} selectedCity - Pre-selected city value (optional)
 * @param {string} selectedArea - Pre-selected area value (optional)
 */
function initCityAreaDropdowns(citySelectId, areaSelectId, selectedCity = '', selectedArea = '') {
    const citySelect = document.getElementById(citySelectId);
    const areaSelect = document.getElementById(areaSelectId);

    if (!citySelect || !areaSelect) {
        console.error('City or Area select element not found');
        return;
    }

    // Populate city dropdown
    Object.keys(cityAreasData).forEach(city => {
        const option = document.createElement('option');
        option.value = city;
        option.textContent = city;
        if (city === selectedCity) {
            option.selected = true;
        }
        citySelect.appendChild(option);
    });

    // Function to update area dropdown based on selected city
    function updateAreaDropdown(city) {
        // Clear existing options except the first one
        areaSelect.innerHTML = '<option value="">اختر المنطقة</option>';

        if (city && cityAreasData[city]) {
            cityAreasData[city].forEach(area => {
                const option = document.createElement('option');
                option.value = area;
                option.textContent = area;
                if (area === selectedArea) {
                    option.selected = true;
                }
                areaSelect.appendChild(option);
            });
            areaSelect.disabled = false;
        } else {
            areaSelect.disabled = true;
        }
    }

    // Initialize area dropdown if city is pre-selected
    if (selectedCity) {
        updateAreaDropdown(selectedCity);
    }

    // Add event listener for city change
    citySelect.addEventListener('change', function () {
        updateAreaDropdown(this.value);
    });
}
