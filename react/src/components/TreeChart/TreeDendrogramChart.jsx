import React, { useEffect, useRef, useState } from 'react';
import { Chart, registerables } from 'chart.js';
import ChartDataLabels from 'chartjs-plugin-datalabels';

import { 
  DendrogramController, 
  EdgeLine,
  TreeController
} from 'chartjs-chart-graph';

// Register Chart.js components including dendrogram
Chart.register(...registerables, DendrogramController, EdgeLine, TreeController, ChartDataLabels);

const TreeDendrogramChart = ({ data }) => {
  const chartRef = useRef(null);
  const canvasRef = useRef(null);
  const [orientation, setOrientation] = useState('horizontal');

  const createChart = () => {
    if (!canvasRef.current || !data || !Array.isArray(data)) return;

    const ctx = canvasRef.current.getContext('2d');

    // Destroy existing chart instance if it exists
    if (chartRef.current) {
      chartRef.current.destroy();
      chartRef.current = null;
    }

    // Clear canvas completely
    ctx.clearRect(0, 0, canvasRef.current.width, canvasRef.current.height);

    // Real dendrogram chart configuration
    try {
      const chartInstance = new Chart(ctx, {
        type: 'dendrogram',
        plugins: [ChartDataLabels],
        data: {
          labels: chartData.map(d => d.name || `Node ${d.id || 'Unknown'}`),
          datasets: [{
            data: chartData,
          }],
        },
        options: {
          maintainAspectRatio: false,
          responsive: true,
          layout: {
            padding: orientation === 'radial' ? 250 : 128
          },
          tree: {
            orientation: orientation,
            mode: 'dendrogram'
          },
          onClick: (e) => {
            console.log(e);
          },
          plugins: {
            datalabels: {
              align: 'left',
              backgroundColor: 'rgba(255, 255, 255, 0.9)',
              formatter: (v) => {
                let output = v.name || `Node ${v.id || 'Unknown'}`;
                if (v.original !== undefined) {
                  output = output + '\n' + v.original;
                }
                return output;
              },
            },
            legend: {
              display: false,
            }
          },
          elements: {
            point: {
              radius: 5,
              backgroundColor: 'steelblue',
              borderColor: 'white',
              borderWidth: 2
            },
            line: {
              borderColor: '#666',
              borderWidth: 2
            }
          },
        },
      });

      chartRef.current = chartInstance;
    } catch (error) {
      console.error('Error creating dendrogram chart:', error);
    }
  };

  useEffect(() => {
    createChart();

    return () => {
      if (chartRef.current) {
        chartRef.current.destroy();
        chartRef.current = null;
      }
    };
  }, [data, orientation]); // Include orientation in dependencies

  const handleOrientationChange = (evt) => {
    const newOrientation = evt.target.value;
    setOrientation(newOrientation);
  };



  const chartData = data;

  return (
    <>
    <div style={{ width: '100%', height: '1200px' }}>
      <canvas ref={canvasRef}  />
        <select 
          id="orientation" 
          value={orientation} 
          onChange={handleOrientationChange}
        >
          <option value="horizontal">Horizontal</option>
          <option value="radial">Radial</option>
        </select>
    </div>
    <div style={{clear:'both', marginBottom:'64px'}}>&nbsp;</div>
    </>
  );
};
export default TreeDendrogramChart;