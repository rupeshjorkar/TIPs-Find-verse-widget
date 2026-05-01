import React, { useEffect, useState } from 'react';
import TreeDendrogramChart from '../../components/TreeChart/TreeDendrogramChart';
import tipsApi from '../../api/tipsApi';

const TreeView = ({ termId }) => {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const loadData = async () => {
      setLoading(true);
      setError(null);
        // Clean the input to extract just the number
        let cleanTermId = termId;
        if (typeof termId === 'string' && termId.includes('?')) {
            const params = new URLSearchParams(termId.split('?')[1]);
            cleanTermId = params.get('term_id');
        }
        const result = await tipsApi.fetchTreeView(cleanTermId);
      console.log(result) 

      if (result.error) {
        setError(result.error);
      } else {
        setData(result);
      }
      setLoading(false);
    };
    loadData();
  }, [termId]);

  if (loading) {
    return (
      <div className="container">
        <div className="spinner"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div>
        <p>Error loading tree data: {error}</p>
      </div>
    );
  }

  return (
      <TreeDendrogramChart data={data} />
  );
};

export default TreeView;
