import React, { useEffect, useState } from "react";
import Breadcrumb from '@src/layouts/full/shared/breadcrumb/Breadcrumb';
import PageContainer from '@src/components/container/PageContainer';
import ParentCard from '@src/components/shared/ParentCard';
import CustomFormLabel from '@src/components/forms/theme-elements/CustomFormLabel';
import CustomTextField from '@src/components/forms/theme-elements/CustomTextField';
import axios from "axios";
import { Link, useParams } from 'react-router-dom';
import { Divider } from '@mui/material';
import {
  Grid,
  Box,
  Typography,
  FormControl,
  MenuItem,
  RadioGroup,
  FormControlLabel,
  Button,
  SliderValueLabelProps,
  Stack,
  List,
  ListItem,
  ListItemIcon,
  ListItemText,
  CardHeader,
  Paper,
  useTheme,
} from '@mui/material';

const BCrumb = [
  {
    to: '/super-admin/society-list',
    title: 'Society Management',
  },
  {
    title: 'Society Details',
  },
];

const MasterSocietyDetails = () => {

  const { id } = useParams(); // Get the 'id' parameter from the URL
  const [societyData, setSocietyData] = useState(null);
  const [error, setError] = useState(null);
  const theme = useTheme();
  const appUrl = import.meta.env.VITE_API_URL;

  useEffect(() => {
    const fetchData = async () => {
      const apiUrl = `${appUrl}/api/show-society/${id}`;
      try {
        const token = sessionStorage.getItem('authToken');
        const response = await axios.get(apiUrl, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        if (response && response.data && response.data.data) {
          setSocietyData(response.data.data);
        } else {
          console.error("Error: Unexpected response structure", response);
        }
        //console.log(response.data);
      } catch (error) {
        setError(error.response.data.message);
        //console.log('Error',error.response.data.message);
      }
    };

    fetchData();
  }, [id]);
  const customButton = (
    <Link to="/super-admin/society-list"><Button variant="contained" color="primary">
    Back
  </Button></Link>
  );
  return (
    <PageContainer title="Society Details" description="this is Society Details page">
      {/* breadcrumb */}
      <Breadcrumb title="" items={BCrumb} />
      {/* end breadcrumb */} 
  
      <ParentCard title="Society Details" button={customButton}>
        <Grid container spacing={3}>
          <Grid item xs={12} sm={12} lg={12}>
            <Grid container spacing={3} pt={1}>
              <Grid item xs={12} sm={12} lg={6} mt={2} pb={3}>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">Society Name</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{societyData && societyData.society_name ? societyData.society_name : '-'}</Typography>
                  </Grid>
                </Grid>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">Society Unique Code</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{societyData && societyData.society_unique_code ? societyData.society_unique_code : '-'}</Typography>
                  </Grid>
                </Grid>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">Phone Number</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{societyData && societyData.phone_number ? societyData.phone_number : '-'}</Typography>
                  </Grid>
                </Grid>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">Email ID</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{societyData && societyData.email ? societyData.email : '-'}  </Typography>
                  </Grid>
                </Grid>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">Address</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{societyData && societyData.address ? societyData.address : '-'}</Typography>
                  </Grid>
                </Grid>
              </Grid>
              <Grid item xs={12} sm={12} lg={6} pb={3}>
                <Typography fontWeight={600} mb={2} variant="h6"></Typography>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">Country</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{societyData && societyData.country_id ? societyData.country_id : '-'}</Typography>
                  </Grid>
                </Grid>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">State</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{societyData && societyData.state_id ? societyData.state_id : '-'}</Typography>
                  </Grid>
                </Grid>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">City</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{societyData && societyData.city ? societyData.city : '-'}</Typography>
                  </Grid>
                </Grid>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">Zipcode</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{societyData && societyData.zipcode ? societyData.zipcode : '-'}</Typography>
                  </Grid>
                </Grid>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">Created On</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{societyData && societyData.created_at ? societyData.created_at : '-'}</Typography>
                  </Grid>
                </Grid>
              </Grid>
            </Grid>
          </Grid>
        </Grid>
      </ParentCard>
    </PageContainer>
  );
};

export default MasterSocietyDetails;