import React from 'react';

import { Grid, Typography, Box, Breadcrumbs, Link, Theme } from '@mui/material';

import { NavLink } from 'react-router-dom';

 

import breadcrumbImg from '@src/assets/images/breadcrumb/ChatBc.png';

import { IconCircle } from '@tabler/icons';

 

interface BreadCrumbType {

  subtitle?: string;

  items?: any[];

  title: string;

  children?: JSX.Element;

}

 

const Breadcrumb = ({ subtitle, items, title, children }: BreadCrumbType) => (

  <Grid

    container

    sx={{

      backgroundColor: 'primary.light',
      borderRadius: (theme: Theme) => theme.shape.borderRadius / 4,

      p: '5px 15px 5px',

      marginBottom: '30px',

      position: 'relative',

      overflow: 'hidden',

    }}

  >

    <Grid item xs={12} sm={6} lg={8} mb={1} >

      <Breadcrumbs

        separator={

          <IconCircle

            size="5"

            fill="textSecondary"

            fillOpacity={'0.6'}

            style={{ margin: '0 5px' }}

          />

        }

        sx={{ alignItems: 'center', mt: items ? '10px' : '' }}

        aria-label="breadcrumb"

      >

        {items

          ? items.map((item) => (

              <div key={item.title}>

                {item.to ? (

                  <Link underline="none" color="inherit" component={NavLink} to={item.to}>

                    {item.title}

                  </Link>

                ) : (

                  <Typography color="textPrimary">{item.title}</Typography>

                )}

              </div>

            ))

          : ''}

      </Breadcrumbs>

    </Grid>

  </Grid>

);

 

export default Breadcrumb;