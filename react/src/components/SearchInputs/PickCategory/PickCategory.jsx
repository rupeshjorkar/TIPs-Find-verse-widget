import React, { useEffect, useState } from 'react';
import Select from 'react-select';
import tipsApi from '../../../api/tipsApi';
import './PickCategory.css';

const PickCategory = ({categorySlug}) => {
  const [categories, setCategories] = useState([]);
  const [selected, setSelected] = useState(null);

  useEffect(() => {
    const loadCategories = async () => {
      const data = await tipsApi.fetchCategoryList();
      setCategories(
        data.map(cat => ({
          value: cat.category_slug,
          label: cat.category
        }))
      );
    };
    loadCategories();
  }, []);

  const handleSelect = (option) => {
    setSelected(option);
    console.log('Selected:', option?.value);
    categorySlug(option?.value)
  };

  const handleButtonClick = () => {
    console.log('Selected:', selected?.value);
    categorySlug(selected?.value)
  };

  return (
    <div id="find-cat">
      <div className="category-search">
        <Select
          options={categories}
          value={selected}
          onChange={handleSelect} 
          placeholder="Pick category"
          isClearable
        />
        <input
          type="submit"
          value="Select"
          id="cat-sercch-btn"
          onClick={handleButtonClick} 
        />
      </div>
    </div>
  );
};

export default PickCategory;
